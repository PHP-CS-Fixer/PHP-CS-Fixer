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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

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
    private const CLASS_CONST_SUPPORTED_HINTS = [
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

    private const CLASS_PROPERTY_SUPPORTED_HINTS = [
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
        'static' => true,
        'string' => true,
    ];

    private const TYPE_SEPARATION_TYPES = [
        CT::T_TYPE_ALTERNATION,
        CT::T_TYPE_INTERSECTION,
        CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN,
        CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE,
    ];

    /**
     * https://secure.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.
     *
     * self     PHP 5.0
     * array    PHP 5.1
     * callable PHP 5.4
     * bool     PHP 7.0
     * float    PHP 7.0
     * int      PHP 7.0
     * string   PHP 7.0
     * iterable PHP 7.1
     * void     PHP 7.1
     * object   PHP 7.2
     * static   PHP 8.0 (return type only)
     * mixed    PHP 8.0
     * false    PHP 8.0 (union return type only)
     * null     PHP 8.0 (union return type only)
     * never    PHP 8.1 (return type only)
     * true     PHP 8.2 (standalone type: https://wiki.php.net/rfc/true-type)
     * false    PHP 8.2 (standalone type: https://wiki.php.net/rfc/null-false-standalone-types)
     * null     PHP 8.2 (standalone type: https://wiki.php.net/rfc/null-false-standalone-types)
     *
     * @var array<string, true>
     */
    private array $functionTypeHints;

    private FunctionsAnalyzer $functionsAnalyzer;

    /**
     * @var list<array{int}|string>
     */
    private array $beforePropertyTypeTokens;

    public function __construct()
    {
        parent::__construct();

        $this->beforePropertyTypeTokens = ['{', ';', [T_PRIVATE], [T_PROTECTED], [T_PUBLIC], [T_VAR]];

        $this->functionTypeHints = [
            'array' => true,
            'bool' => true,
            'callable' => true,
            'float' => true,
            'int' => true,
            'iterable' => true,
            'object' => true,
            'self' => true,
            'string' => true,
            'void' => true,
        ];

        if (\PHP_VERSION_ID >= 8_00_00) {
            $this->functionTypeHints['false'] = true;
            $this->functionTypeHints['mixed'] = true;
            $this->functionTypeHints['null'] = true;
            $this->functionTypeHints['static'] = true;
        }

        if (\PHP_VERSION_ID >= 8_01_00) {
            $this->functionTypeHints['never'] = true;

            $this->beforePropertyTypeTokens[] = [T_READONLY];
        }

        if (\PHP_VERSION_ID >= 8_02_00) {
            $this->functionTypeHints['true'] = true;
        }

        $this->functionsAnalyzer = new FunctionsAnalyzer();
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Native type declarations should be used in the correct case.',
            [
                new CodeSample(
                    "<?php\nclass Bar {\n    public function Foo(CALLABLE \$bar): INT\n    {\n        return 1;\n    }\n}\n"
                ),
                new VersionSpecificCodeSample(
                    "<?php\nclass Foo\n{\n    const INT BAR = 1;\n}\n",
                    new VersionSpecification(8_03_00),
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        $classyFound = $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());

        return
            $tokens->isAnyTokenKindsFound([T_FUNCTION, T_FN])
            || ($classyFound && $tokens->isTokenKindFound(T_STRING))
            || (
                \PHP_VERSION_ID >= 8_03_00
                && $tokens->isTokenKindFound(T_CONST)
                && $classyFound
            );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->fixFunctions($tokens);
        $this->fixClassConstantsAndProperties($tokens);
    }

    private function fixFunctions(Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind([T_FUNCTION, T_FN])) {
                $this->fixFunctionReturnType($tokens, $index);
                $this->fixFunctionArgumentTypes($tokens, $index);
            }
        }
    }

    private function fixFunctionArgumentTypes(Tokens $tokens, int $index): void
    {
        foreach ($this->functionsAnalyzer->getFunctionArguments($tokens, $index) as $argument) {
            $this->fixArgumentType($tokens, $argument->getTypeAnalysis());
        }
    }

    private function fixFunctionReturnType(Tokens $tokens, int $index): void
    {
        $this->fixArgumentType($tokens, $this->functionsAnalyzer->getFunctionReturnType($tokens, $index));
    }

    private function fixArgumentType(Tokens $tokens, ?TypeAnalysis $type = null): void
    {
        if (null === $type) {
            return;
        }

        for ($index = $type->getStartIndex(); $index <= $type->getEndIndex(); ++$index) {
            if ($tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(T_NS_SEPARATOR)) {
                continue;
            }

            $this->fixCasing($this->functionTypeHints, $tokens, $index);
        }
    }

    private function fixClassConstantsAndProperties(Tokens $tokens): void
    {
        $analyzer = new TokensAnalyzer($tokens);
        $elements = array_reverse($analyzer->getClassyElements(), true);

        foreach ($elements as $index => $element) {
            if ('const' === $element['type']) {
                if (\PHP_VERSION_ID >= 8_03_00 && !$this->isConstWithoutType($tokens, $index)) {
                    foreach ($this->getNativeTypeHintCandidatesForConstant($tokens, $index) as $nativeTypeHintIndex) {
                        $this->fixCasing($this::CLASS_CONST_SUPPORTED_HINTS, $tokens, $nativeTypeHintIndex);
                    }
                }

                continue;
            }

            if ('property' === $element['type']) {
                foreach ($this->getNativeTypeHintCandidatesForProperty($tokens, $index) as $nativeTypeHintIndex) {
                    $this->fixCasing($this::CLASS_PROPERTY_SUPPORTED_HINTS, $tokens, $nativeTypeHintIndex);
                }
            }
        }
    }

    /** @return iterable<int> */
    private function getNativeTypeHintCandidatesForConstant(Tokens $tokens, int $index): iterable
    {
        $constNameIndex = $this->getConstNameIndex($tokens, $index);
        $index = $this->getFirstIndexOfType($tokens, $index);

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

    /** @return iterable<int> */
    private function getNativeTypeHintCandidatesForProperty(Tokens $tokens, int $index): iterable
    {
        $propertyNameIndex = $index;
        $index = $tokens->getPrevTokenOfKind($index, $this->beforePropertyTypeTokens);

        $index = $this->getFirstIndexOfType($tokens, $index);

        do {
            $typeEnd = $this->getTypeEnd($tokens, $index, $propertyNameIndex);

            if ($typeEnd === $index) {
                yield $index;
            }

            do {
                $index = $tokens->getNextMeaningfulToken($index);
            } while ($tokens[$index]->isGivenKind(self::TYPE_SEPARATION_TYPES));
        } while ($index < $propertyNameIndex);

        return [];
    }

    private function getFirstIndexOfType(Tokens $tokens, int $index): int
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

    /**
     * @param array<string, true> $supportedTypeHints
     */
    private function fixCasing(array $supportedTypeHints, Tokens $tokens, int $index): void
    {
        $typeContent = $tokens[$index]->getContent();
        $typeContentLower = strtolower($typeContent);

        if (isset($supportedTypeHints[$typeContentLower]) && $typeContent !== $typeContentLower) {
            $tokens[$index] = new Token([$tokens[$index]->getId(), $typeContentLower]);
        }
    }
}
