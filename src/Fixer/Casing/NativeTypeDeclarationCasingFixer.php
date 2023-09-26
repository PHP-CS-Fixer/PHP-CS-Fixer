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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NativeTypeDeclarationCasingFixer extends AbstractFixer
{
    /*
     * https://wiki.php.net/rfc/typed_class_constants
     * Supported types
     * Class constant type declarations support all type declarations supported by PHP,
     * except `void`, `callable`, `never`.
     *
     * array
     * bool
     * callable
     * float
     * int
     * iterable
     * object
     * mixed
     * parent
     * self
     * string
     * any class or interface name -> not native, so not applicable for this Fixer
     * ?type -> not native, `?` has no casing, so not applicable for this Fixer
     *
     * Not in the list referenced but supported:
     * null
     * static
     */
    private const SUPPORTED_HINTS = [
        'array' => true,
        'bool' => true,
        'float' => true,
        'int' => true,
        'iterable' => true,
        'mixed' => true,
        'null' => true,
        'object' => true,
        'parent' => true,
        'self' => true,
        'string' => true,
        'static' => true,
    ];

    private const TYPE_SEPARATION_TYPES = [
        CT::T_TYPE_ALTERNATION,
        CT::T_TYPE_INTERSECTION,
        CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
        CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Native type hints for constants should use the correct case.',
            [
                new VersionSpecificCodeSample(
                    "<?php\nclass Foo\n{\n    const INT BAR = 1;\n}\n",
                    new VersionSpecification(8_03_00),
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_03_00
            && $tokens->isTokenKindFound(T_CONST)
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CONST) || $this->isConstWithoutType($tokens, $index)) {
                continue;
            }

            foreach ($this->getNativeTypeHintCandidates($tokens, $index) as $nativeTypeHintIndex) {
                $this->fixCasing($tokens, $nativeTypeHintIndex);
            }
        }
    }

    /** @return iterable<int> */
    private function getNativeTypeHintCandidates(Tokens $tokens, int $index): iterable
    {
        $constNameIndex = $this->getConstNameIndex($tokens, $index);
        $index = $this->getConstTypeFirstIndex($tokens, $index);

        do {
            $typeEnd = $this->getTypeEnd($tokens, $index, $constNameIndex);

            if ($typeEnd === $index) {
                yield $index;
            }

            do {
                $index = $tokens->getNextMeaningfulToken($index);
            } while ($tokens[$index]->isGivenKind(self::TYPE_SEPARATION_TYPES));
        } while ($index < $constNameIndex);
    }

    private function getTypeEnd(Tokens $tokens, int $index, int $upperLimit): int
    {
        if (!$tokens[$index]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
            return $index; // callable, array, self, static, etc.
        }

        $endIndex = $index;
        while ($tokens[$index]->isGivenKind([T_STRING, T_NS_SEPARATOR]) && $index < $upperLimit) {
            $endIndex = $index;
            $index = $tokens->getNextMeaningfulToken($index);
        }

        return $endIndex;
    }

    private function isConstWithoutType(Tokens $tokens, int $index): bool
    {
        $index = $tokens->getNextMeaningfulToken($index);

        return $tokens[$index]->isGivenKind(T_STRING) && $tokens[$tokens->getNextMeaningfulToken($index)]->equals('=');
    }

    private function getConstNameIndex(Tokens $tokens, int $index): int
    {
        return $tokens->getPrevMeaningfulToken(
            $tokens->getNextTokenOfKind($index, ['=']),
        );
    }

    private function getConstTypeFirstIndex(Tokens $tokens, int $index): int
    {
        $index = $tokens->getNextMeaningfulToken($index);

        if ($tokens[$index]->isGivenKind(CT::T_NULLABLE_TYPE)) {
            $index = $tokens->getNextMeaningfulToken($index);
        }

        if ($tokens[$index]->isGivenKind(CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN)) {
            $index = $tokens->getNextMeaningfulToken($index);
        }

        return $index;
    }

    private function fixCasing(Tokens $tokens, int $index): void
    {
        $typeContent = $tokens[$index]->getContent();
        $typeContentLower = strtolower($typeContent);

        if (isset($this::SUPPORTED_HINTS[$typeContentLower]) && $typeContent !== $typeContentLower) {
            $tokens[$index] = new Token([$tokens[$index]->getId(), $typeContentLower]);
        }
    }
}
