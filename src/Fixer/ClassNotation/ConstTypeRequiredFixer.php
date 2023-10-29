<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ConstTypeRequiredFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Class constants must be typed.',
            [
                new VersionSpecificCodeSample(
                    '<?php
interface Doc
{
    const URL = "https://github.com/FriendsOfPHP/PHP-CS-Fixer/";
}
',
                    new VersionSpecification(8_03_00),
                ),
            ],
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after NoUnneededControlParenthesesFixer, NoUselessConcatOperatorFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_03_00
            && $tokens->isTokenKindFound(T_CONST)
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->getSize() - 2; $index > 0; --$index) {
            if ($tokens[$index]->isClassy()) {
                $this->fixClassyDefinition($tokens, $index);
            }
        }
    }

    /**
     * @param int $index Class definition token start index
     */
    private function fixClassyDefinition(Tokens $tokens, int $index): void
    {
        $candidates = [];
        $index = $tokens->getNextTokenOfKind($index, ['{']);

        do {
            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->isGivenKind(T_CONST)) {
                $candidates[] = $index;

                continue;
            }

            $blockType = Tokens::detectBlockType($tokens[$index]);

            if (null !== $blockType && true === $blockType['isStart']) {
                $index = $tokens->findBlockEnd($blockType['type'], $index);
            }
        } while (!$tokens[$index]->equals('}'));

        foreach (array_reverse($candidates) as $constIndex) {
            $this->fixConstant($tokens, $constIndex);
        }
    }

    private function fixConstant(Tokens $tokens, int $constIndex): void
    {
        $index = $tokens->getNextMeaningfulToken($constIndex);

        if (!$tokens[$index]->isGivenKind(T_STRING)) {
            return; // constant already has a type, skip it
        }

        $index = $tokens->getNextMeaningfulToken($index);

        if (!$tokens[$index]->equals('=')) {
            return; // constant already has a type, skip it
        }

        $valueIndex = $tokens->getNextMeaningfulToken($index);
        $possibleEndIndex = $tokens->getNextMeaningfulToken($valueIndex);

        // Fix constant which are like `const NAME = X;`
        if ($tokens[$possibleEndIndex]->equals(';')) {
            $this->fixSimpleConstant($tokens, $constIndex, $valueIndex);

            return;
        }

        // Fix constant which are like `const NAME = [X](.+);` or `const NAME = array(X)(.+);`
        if ($tokens[$valueIndex]->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
            $this->fixArrayConstant($tokens, $constIndex, $valueIndex);

            return;
        }

        // Fix constant which are like `const NAME = \X;`
        if (
            $tokens[$valueIndex]->isGivenKind(T_NS_SEPARATOR)
            && $tokens[$tokens->getNextMeaningfulToken($possibleEndIndex)]->equals(';')
        ) {
            $valueIndex = $tokens->getNextMeaningfulToken($valueIndex);
            $this->fixSimpleStringConstant($tokens, $constIndex, $valueIndex);

            return;
        }

        // Fix constant which are like `const NAME = (((\|NS\)X)|self)::class;`
        if ($this->isMagicClassReferenceWithoutFollowupOperation($tokens, $valueIndex)) {
            $this->insertTypeForConst($tokens, $constIndex + 1, 'string');

            return;
        }

        // all other `const` cases were we cannot reliably and safely determine the type of
        $this->insertTypeForConst($tokens, $constIndex + 1, 'mixed');
    }

    private function fixSimpleConstant(
        Tokens $tokens,
        int $constIndex,
        int $valueIndex
    ): void {
        if ($tokens[$valueIndex]->isGivenKind(T_STRING)) {
            $this->fixSimpleStringConstant($tokens, $constIndex, $valueIndex);

            return;
        }

        $token = $tokens[$valueIndex];
        $type = null;

        if ($token->isGivenKind(T_LNUMBER)) {
            $type = 'int';
        } elseif ($token->isGivenKind(T_DNUMBER)) {
            $type = 'float';
        } elseif ($token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
            $type = 'string';
        } elseif ($this->isMagicConstOfTypeStr($token)) {
            $type = 'string';
        } elseif ($this->isMagicConstOfTypeInt($token)) {
            $type = 'int';
        }

        $this->insertTypeForConst($tokens, $constIndex + 1, $type ?? 'mixed');
    }

    private function fixSimpleStringConstant(
        Tokens $tokens,
        int $constIndex,
        int $valueIndex
    ): void {
        $type = $this->getTypeForStringToken($tokens[$valueIndex]);
        $this->insertTypeForConst($tokens, $constIndex + 1, $type ?? 'mixed');
    }

    private function fixArrayConstant(
        Tokens $tokens,
        int $constIndex,
        int $valueIndex
    ): void {
        if ($tokens[$valueIndex]->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $valueIndex);
        } else {
            \assert($tokens[$valueIndex]->isGivenKind(T_ARRAY));
            $endIndex = $tokens->getNextMeaningfulToken($valueIndex);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $endIndex);
        }

        $endIndex = $tokens->getNextMeaningfulToken($endIndex);
        $this->insertTypeForConst($tokens, $constIndex + 1, $tokens[$endIndex]->equals(';') ? 'array' : 'mixed');
    }

    private function insertTypeForConst(Tokens $tokens, int $index, string $type): void
    {
        $tokens->insertSlices(
            [
                $index => [
                    new Token([T_WHITESPACE, ' ']),
                    new Token(['array' === $type ? CT::T_ARRAY_TYPEHINT : T_STRING, $type]),
                ],
            ],
        );
    }

    private function getTypeForStringToken(Token $token): ?string
    {
        \assert($token->isGivenKind(T_STRING));

        $content = $token->getContent();
        $contentLower = strtolower($content);

        if ('true' === $contentLower || 'false' === $contentLower || 'null' === $contentLower) {
            $type = $contentLower;
        } elseif ($this->isPredefinedConstStr($content)) {
            $type = 'string';
        } elseif ($this->isPredefinedConstInt($content)) {
            $type = 'int';
        } elseif ($this->isPredefinedConstFloat($content)) {
            $type = 'float';
        } else {
            $type = null;
        }

        return $type;
    }

    private function isMagicConstOfTypeInt(Token $token): bool
    {
        $magicConstInt = [
            T_LINE,
        ];

        return $token->isGivenKind($magicConstInt);
    }

    private function isMagicConstOfTypeStr(Token $token): bool
    {
        $magicConstStr = [
            T_FILE,
            T_DIR,
            T_CLASS_C,
            T_NS_C,
            T_TRAIT_C,
        ];

        return $token->isGivenKind($magicConstStr);
    }

    private function isPredefinedConstStr(string $content): bool
    {
        static $predefinedConstStr = null;

        if (null === $predefinedConstStr) {
            $predefinedConstStr = [
                'DEFAULT_INCLUDE_PATH',
                'PEAR_EXTENSION_DIR',
                'PEAR_INSTALL_DIR',
                'PHP_BINARY',
                'PHP_BINDIR',
                'PHP_CONFIG_FILE_PATH',
                'PHP_CONFIG_FILE_SCAN_DIR',
                'PHP_DATADIR',
                'PHP_EOL',
                'PHP_EXTENSION_DIR',
                'PHP_EXTRA_VERSION',
                'PHP_FD_SETSIZE',
                'PHP_LIBDIR',
                'PHP_LOCALSTATEDIR',
                'PHP_MANDIR',
                'PHP_OS',
                'PHP_OS_FAMILY',
                'PHP_PREFIX',
                'PHP_SAPI',
                'PHP_SHLIB_SUFFIX',
                'PHP_SYSCONFDIR',
                'PHP_VERSION',
            ];
        }

        return \in_array($content, $predefinedConstStr, true);
    }

    private function isPredefinedConstInt(string $content): bool
    {
        static $predefinedConstInt = null;

        if (null === $predefinedConstInt) {
            $predefinedConstInt = [
                'E_ALL',
                'E_COMPILE_ERROR',
                'E_COMPILE_WARNING',
                'E_CORE_ERROR',
                'E_CORE_WARNING',
                'E_DEPRECATED',
                'E_ERROR',
                'E_NOTICE',
                'E_PARSE',
                'E_RECOVERABLE_ERROR',
                'E_STRICT',
                'E_USER_DEPRECATED',
                'E_USER_ERROR',
                'E_USER_NOTICE',
                'E_USER_WARNING',
                'E_WARNING',
                'PHP_DEBUG',
                'PHP_FLOAT_DIG',
                'PHP_INT_MAX',
                'PHP_INT_MIN',
                'PHP_INT_SIZE',
                'PHP_MAJOR_VERSION',
                'PHP_MAXPATHLEN',
                'PHP_MINOR_VERSION',
                'PHP_RELEASE_VERSION',
                'PHP_VERSION_ID',
                'PHP_WINDOWS_EVENT_CTRL_BREAK',
                'PHP_WINDOWS_EVENT_CTRL_C',
                'PHP_ZTS',
                '__COMPILER_HALT_OFFSET__',
            ];
        }

        return \in_array($content, $predefinedConstInt, true);
    }

    private function isPredefinedConstFloat(string $content): bool
    {
        static $predefinedConstFloat = null;

        if (null === $predefinedConstFloat) {
            $predefinedConstFloat = [
                'PHP_FLOAT_EPSILON',
                'PHP_FLOAT_MAX',
                'PHP_FLOAT_MIN',
            ];
        }

        return \in_array($content, $predefinedConstFloat, true);
    }

    private function isMagicClassReferenceWithoutFollowupOperation(
        Tokens $tokens,
        int $valueIndex
    ): bool {
        if (!$tokens[$valueIndex]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            return false;
        }

        do {
            $valueIndex = $tokens->getNextMeaningfulToken($valueIndex);
        } while ($tokens[$valueIndex]->isGivenKind([T_NS_SEPARATOR, T_STRING]));

        if (!$tokens[$valueIndex]->isGivenKind(T_DOUBLE_COLON)) {
            return false;
        }

        $valueIndex = $tokens->getNextMeaningfulToken($valueIndex);

        if (!$tokens[$valueIndex]->isGivenKind(CT::T_CLASS_CONSTANT)) {
            return false;
        }

        $endIndex = $tokens->getNextMeaningfulToken($valueIndex);

        return $tokens[$endIndex]->equals(';');
    }
}
