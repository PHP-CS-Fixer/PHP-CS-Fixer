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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 */
final class OrderedTypesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Sort union types and intersection types using configured order.',
            [
                new CodeSample(
                    '<?php
try {
    cache()->save($foo);
} catch (\RuntimeException|CacheException $e) {
    logger($e);

    throw $e;
}
'
                ),
                new VersionSpecificCodeSample(
                    '<?php
interface Foo
{
    public function bar(null|string|int $foo): string|int;

    public function foo(\Stringable&\Countable $obj): int;
}
',
                    new VersionSpecification(80100),
                    ['null_adjustment' => 'always_last']
                ),
                new VersionSpecificCodeSample(
                    '<?php
interface Bar
{
    public function bar(null|string|int $foo): string|int;
}
',
                    new VersionSpecification(80000),
                    [
                        'sort_algorithm' => 'none',
                        'null_adjustment' => 'always_last',
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before TypesSpacesFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([CT::T_TYPE_ALTERNATION, CT::T_TYPE_INTERSECTION]);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('sort_algorithm', 'Whether the types should be sorted alphabetically, or not sorted.'))
                ->setAllowedValues(['alpha', 'none'])
                ->setDefault('alpha')
                ->getOption(),
            (new FixerOptionBuilder('null_adjustment', 'Forces the position of `null` (overrides `sort_algorithm`).'))
                ->setAllowedValues(['always_first', 'always_last', 'none'])
                ->setDefault('always_first')
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionsAnalyzer = new FunctionsAnalyzer();

        foreach ($this->getElements($tokens) as $index => $type) {
            if ('catch' === $type) {
                $this->fixCatchArgumentType($tokens, $index);

                continue;
            }

            if ('property' === $type) {
                $this->fixPropertyType($tokens, $index);

                continue;
            }

            $this->fixMethodArgumentType($functionsAnalyzer, $tokens, $index);
            $this->fixMethodReturnType($functionsAnalyzer, $tokens, $index);
        }
    }

    /**
     * @return array<int, string>
     *
     * @phpstan-return array<int, 'catch'|'method'|'property'>
     */
    private function getElements(Tokens $tokens): array
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        $elements = array_map(
            static fn (array $element): string => $element['type'],
            array_filter(
                $tokensAnalyzer->getClassyElements(),
                static fn (array $element): bool => \in_array($element['type'], ['method', 'property'], true)
            )
        );

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_CATCH)) {
                $elements[$index] = 'catch';

                continue;
            }

            if (
                $token->isGivenKind(T_FN)
                || ($token->isGivenKind(T_FUNCTION) && !isset($elements[$index]))
            ) {
                $elements[$index] = 'method';
            }
        }

        return $elements;
    }

    private function collectTypeAnalysis(Tokens $tokens, int $startIndex, int $endIndex): ?TypeAnalysis
    {
        $type = '';
        $typeStartIndex = $tokens->getNextMeaningfulToken($startIndex);
        $typeEndIndex = $typeStartIndex;

        for ($i = $typeStartIndex; $i < $endIndex; ++$i) {
            if ($tokens[$i]->isWhitespace() || $tokens[$i]->isComment()) {
                continue;
            }

            $type .= $tokens[$i]->getContent();
            $typeEndIndex = $i;
        }

        return '' !== $type ? new TypeAnalysis($type, $typeStartIndex, $typeEndIndex) : null;
    }

    private function fixCatchArgumentType(Tokens $tokens, int $index): void
    {
        $catchStart = $tokens->getNextTokenOfKind($index, ['(']);
        $catchEnd = $tokens->getNextTokenOfKind($catchStart, [')', [T_VARIABLE]]);

        $catchArgumentType = $this->collectTypeAnalysis($tokens, $catchStart, $catchEnd);

        if (null === $catchArgumentType || !$this->isTypeSortable($catchArgumentType)) {
            return; // nothing to fix
        }

        $this->sortTypes($catchArgumentType, $tokens);
    }

    private function fixPropertyType(Tokens $tokens, int $index): void
    {
        $propertyIndex = $index;
        $propertyModifiers = [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC, T_VAR];

        if (\defined('T_READONLY')) {
            $propertyModifiers[] = T_READONLY; // @TODO drop condition when PHP 8.1 is supported
        }

        do {
            $index = $tokens->getPrevMeaningfulToken($index);
        } while (!$tokens[$index]->isGivenKind($propertyModifiers));

        $propertyType = $this->collectTypeAnalysis($tokens, $index, $propertyIndex);

        if (null === $propertyType || !$this->isTypeSortable($propertyType)) {
            return; // nothing to fix
        }

        $this->sortTypes($propertyType, $tokens);
    }

    private function fixMethodArgumentType(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index): void
    {
        foreach ($functionsAnalyzer->getFunctionArguments($tokens, $index) as $argumentInfo) {
            $argumentType = $argumentInfo->getTypeAnalysis();

            if (null === $argumentType || !$this->isTypeSortable($argumentType)) {
                continue; // nothing to fix
            }

            $this->sortTypes($argumentType, $tokens);
        }
    }

    private function fixMethodReturnType(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index): void
    {
        $returnType = $functionsAnalyzer->getFunctionReturnType($tokens, $index);

        if (null === $returnType || !$this->isTypeSortable($returnType)) {
            return; // nothing to fix
        }

        $this->sortTypes($returnType, $tokens);
    }

    private function sortTypes(TypeAnalysis $typeAnalysis, Tokens $tokens): void
    {
        $type = $typeAnalysis->getName();

        if (str_contains($type, '|') && str_contains($type, '&')) {
            // a DNF type of the form (A&B)|C, available as of PHP 8.2
            [$originalTypes, $glue] = $this->collectDisjunctiveNormalFormTypes($type);
        } else {
            [$originalTypes, $glue] = $this->collectUnionOrIntersectionTypes($type);
        }

        // If the $types array is coming from a DNF type, then we have parts
        // which are also array. If so, we sort those sub-types first before
        // running the sorting algorithm to the entire $types array.
        $sortedTypes = array_map(function ($subType) {
            if (\is_array($subType)) {
                return $this->runTypesThroughSortingAlgorithm($subType);
            }

            return $subType;
        }, $originalTypes);

        $sortedTypes = $this->runTypesThroughSortingAlgorithm($sortedTypes);

        if ($sortedTypes === $originalTypes) {
            return;
        }

        $tokens->overrideRange(
            $typeAnalysis->getStartIndex(),
            $typeAnalysis->getEndIndex(),
            $this->createTypeDeclarationTokens($sortedTypes, $glue)
        );
    }

    private function isTypeSortable(TypeAnalysis $type): bool
    {
        return str_contains($type->getName(), '|') || str_contains($type->getName(), '&');
    }

    /**
     * @return array{0: array<string|string[]>, 1: string}
     */
    private function collectDisjunctiveNormalFormTypes(string $type): array
    {
        $types = array_map(static function ($subType) {
            if (str_starts_with($subType, '(')) {
                return explode('&', trim($subType, '()'));
            }

            return $subType;
        }, explode('|', $type));

        return [$types, '|'];
    }

    /**
     * @return array{0: string[], 1: string}
     */
    private function collectUnionOrIntersectionTypes(string $type): array
    {
        $types = explode('|', $type);
        $glue = '|';

        if (1 === \count($types)) {
            $types = explode('&', $type);
            $glue = '&';
        }

        return [$types, $glue];
    }

    /**
     * @param array<string|string[]> $types
     *
     * @return array<string|string[]>
     */
    private function runTypesThroughSortingAlgorithm(array $types): array
    {
        $normalizeType = static fn (string $type): string => Preg::replace('/^\\\\?/', '', $type);

        usort($types, function ($a, $b) use ($normalizeType): int {
            if (\is_array($a)) {
                $a = implode('&', $a);
            }

            if (\is_array($b)) {
                $b = implode('&', $b);
            }

            $a = $normalizeType($a);
            $b = $normalizeType($b);
            $lowerCaseA = strtolower($a);
            $lowerCaseB = strtolower($b);

            if ('none' !== $this->configuration['null_adjustment']) {
                if ('null' === $lowerCaseA && 'null' !== $lowerCaseB) {
                    return 'always_last' === $this->configuration['null_adjustment'] ? 1 : -1;
                }

                if ('null' !== $lowerCaseA && 'null' === $lowerCaseB) {
                    return 'always_last' === $this->configuration['null_adjustment'] ? -1 : 1;
                }
            }

            if ('alpha' === $this->configuration['sort_algorithm']) {
                return strcasecmp($a, $b);
            }

            return 0;
        });

        return $types;
    }

    /**
     * @param array<int, string|string[]> $types
     *
     * @return array<int, Token>
     */
    private function createTypeDeclarationTokens(array $types, string $glue, bool $isDisjunctive = false): array
    {
        static $specialTypes = [
            'array' => [CT::T_ARRAY_TYPEHINT, 'array'],
            'callable' => [T_CALLABLE, 'callable'],
            'static' => [T_STATIC, 'static'],
        ];

        static $glues = [
            '|' => [CT::T_TYPE_ALTERNATION, '|'],
            '&' => [CT::T_TYPE_INTERSECTION, '&'],
        ];

        $count = \count($types);
        $newTokens = [];

        foreach ($types as $i => $type) {
            if (\is_array($type)) {
                $newTokens = array_merge(
                    $newTokens,
                    $this->createTypeDeclarationTokens($type, '&', true)
                );
            } elseif (isset($specialTypes[$type])) {
                $newTokens[] = new Token($specialTypes[$type]);
            } else {
                foreach (explode('\\', $type) as $nsIndex => $value) {
                    if (0 === $nsIndex && '' === $value) {
                        continue;
                    }

                    if ($nsIndex > 0) {
                        $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
                    }

                    $newTokens[] = new Token([T_STRING, $value]);
                }
            }

            if ($i <= $count - 2) {
                $newTokens[] = new Token($glues[$glue]);
            }
        }

        if ($isDisjunctive) {
            array_unshift($newTokens, new Token([CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, '(']));
            $newTokens[] = new Token([CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE, ')']);
        }

        return $newTokens;
    }
}
