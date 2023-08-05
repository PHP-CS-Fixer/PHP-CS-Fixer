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

namespace PhpCsFixer\Fixer\FunctionNotation;

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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author HypeMC
 */
final class NullableTypeDeclarationForDefaultNullValueFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Adds or removes `?` before single type declarations or `|null` at the end of union types when parameters have a default `null` value.',
            [
                new CodeSample(
                    "<?php\nfunction sample(string \$str = null)\n{}\n"
                ),
                new CodeSample(
                    "<?php\nfunction sample(?string \$str = null)\n{}\n",
                    ['use_nullable_type_declaration' => false]
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction sample(string|int \$str = null)\n{}\n",
                    new VersionSpecification(8_00_00)
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction sample(string|int|null \$str = null)\n{}\n",
                    new VersionSpecification(8_00_00),
                    ['use_nullable_type_declaration' => false]
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction sample(\\Foo&\\Bar \$str = null)\n{}\n",
                    new VersionSpecification(8_02_00)
                ),
                new VersionSpecificCodeSample(
                    "<?php\nfunction sample((\\Foo&\\Bar)|null \$str = null)\n{}\n",
                    new VersionSpecification(8_02_00),
                    ['use_nullable_type_declaration' => false]
                ),
            ],
            'Rule is applied only in a PHP 7.1+ environment.'
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_VARIABLE) && $tokens->isAnyTokenKindsFound([T_FUNCTION, T_FN]);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoUnreachableDefaultArgumentValueFixer, NullableTypeDeclarationFixer, OrderedTypesFixer.
     */
    public function getPriority(): int
    {
        return 3;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('use_nullable_type_declaration', 'Whether to add or remove `?` or `|null` to parameters with a default `null` value.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionsAnalyzer = new FunctionsAnalyzer();
        $tokenKinds = [T_FUNCTION, T_FN];

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind($tokenKinds)) {
                continue;
            }

            $arguments = $functionsAnalyzer->getFunctionArguments($tokens, $index);
            $this->fixFunctionParameters($tokens, $arguments);
        }
    }

    /**
     * @param ArgumentAnalysis[] $arguments
     */
    private function fixFunctionParameters(Tokens $tokens, array $arguments): void
    {
        $constructorPropertyModifiers = [
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
        ];

        if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
            $constructorPropertyModifiers[] = T_READONLY;
        }

        foreach (array_reverse($arguments) as $argumentInfo) {
            if (
                // Skip, if the parameter
                // - doesn't have a type declaration
                !$argumentInfo->hasTypeAnalysis()
                // - has a mixed or standalone null type
                || \in_array(strtolower($argumentInfo->getTypeAnalysis()->getName()), ['mixed', 'null'], true)
                // - a default value is not null we can continue
                || !$argumentInfo->hasDefault() || 'null' !== strtolower($argumentInfo->getDefault())
            ) {
                continue;
            }

            $argumentTypeInfo = $argumentInfo->getTypeAnalysis();

            if (\PHP_VERSION_ID >= 8_00_00 && false === $this->configuration['use_nullable_type_declaration']) {
                $visibility = $tokens[$tokens->getPrevMeaningfulToken($argumentTypeInfo->getStartIndex())];

                if ($visibility->isGivenKind($constructorPropertyModifiers)) {
                    continue;
                }
            }

            $typeAnalysisName = $argumentTypeInfo->getName();
            if (str_contains($typeAnalysisName, '|') || str_contains($typeAnalysisName, '&')) {
                $this->fixUnionTypeParameter($tokens, $argumentTypeInfo);
            } else {
                $this->fixSingleTypeParameter($tokens, $argumentTypeInfo);
            }
        }
    }

    private function fixSingleTypeParameter(Tokens $tokens, TypeAnalysis $argumentTypeInfo): void
    {
        if (true === $this->configuration['use_nullable_type_declaration']) {
            if (!$argumentTypeInfo->isNullable()) {
                $tokens->insertAt($argumentTypeInfo->getStartIndex(), new Token([CT::T_NULLABLE_TYPE, '?']));
            }
        } elseif ($argumentTypeInfo->isNullable()) {
            $tokens->removeTrailingWhitespace($startIndex = $argumentTypeInfo->getStartIndex());
            $tokens->clearTokenAndMergeSurroundingWhitespace($startIndex);
        }
    }

    private function fixUnionTypeParameter(Tokens $tokens, TypeAnalysis $argumentTypeInfo): void
    {
        if (true === $this->configuration['use_nullable_type_declaration']) {
            if ($argumentTypeInfo->isNullable()) {
                return;
            }

            $typeAnalysisName = $argumentTypeInfo->getName();
            $endIndex = $argumentTypeInfo->getEndIndex();

            if (str_contains($typeAnalysisName, '&') && !str_contains($typeAnalysisName, '|')) {
                $endIndex += 2;
                $tokens->insertAt($argumentTypeInfo->getStartIndex(), new Token([CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN, '(']));
                $tokens->insertAt($endIndex, new Token([CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE, ')']));
            }

            $tokens->insertAt($endIndex + 1, [
                new Token([CT::T_TYPE_ALTERNATION, '|']),
                new Token([T_STRING, 'null']),
            ]);
        } elseif ($argumentTypeInfo->isNullable()) {
            $startIndex = $argumentTypeInfo->getStartIndex();

            $index = $tokens->getNextTokenOfKind($startIndex - 1, [[T_STRING, 'null']], false);

            if ($index === $startIndex) {
                $tokens->removeTrailingWhitespace($index);
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);

                $index = $tokens->getNextMeaningfulToken($index);
                if ($tokens[$index]->equals([CT::T_TYPE_ALTERNATION, '|'])) {
                    $tokens->removeTrailingWhitespace($index);
                    $tokens->clearTokenAndMergeSurroundingWhitespace($index);
                }
            } else {
                $tokens->removeLeadingWhitespace($index);
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);

                $index = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$index]->equals([CT::T_TYPE_ALTERNATION, '|'])) {
                    $tokens->removeLeadingWhitespace($index);
                    $tokens->clearTokenAndMergeSurroundingWhitespace($index);
                }
            }

            $typeAnalysisName = $argumentTypeInfo->getName();

            if (str_contains($typeAnalysisName, '&') && 1 === substr_count($typeAnalysisName, '|')) {
                $index = $tokens->getNextTokenOfKind($startIndex - 1, [[CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_OPEN]]);
                $tokens->removeTrailingWhitespace($index);
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);

                $index = $tokens->getPrevTokenOfKind($argumentTypeInfo->getEndIndex() + 1, [[CT::T_DISJUNCTIVE_NORMAL_FORM_TYPE_PARENTHESIS_CLOSE]]);
                $tokens->removeLeadingWhitespace($index);
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            }
        }
    }
}
