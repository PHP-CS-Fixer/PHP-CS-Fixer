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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 */
final class TypeDeclarationSpacesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Ensure single space between a variable and its type declaration in function arguments and properties.',
            [
                new CodeSample(
                    '<?php
class Bar
{
    private string    $a;
    private bool   $b;

    public function __invoke(array   $c) {}
}
'
                ),
                new CodeSample(
                    '<?php
class Foo
{
    public int   $bar;

    public function baz(string     $a)
    {
        return fn(bool    $c): string => (string) $c;
    }
}
',
                    ['elements' => ['function']]
                ),
                new CodeSample(
                    '<?php
class Foo
{
    public int   $bar;

    public function baz(string     $a) {}
}
',
                    ['elements' => ['property']]
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([...Token::getClassyTokenKinds(), T_FN, T_FUNCTION]);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('elements', 'Structural elements where the spacing after the type declaration should be fixed.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([new AllowedValueSubset(['function', 'property'])])
                ->setDefault(['function', 'property'])
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionsAnalyzer = new FunctionsAnalyzer();

        foreach (array_reverse($this->getElements($tokens), true) as $index => $type) {
            if ('property' === $type && \in_array('property', $this->configuration['elements'], true)) {
                $this->ensureSingleSpaceAtPropertyTypehint($tokens, $index);

                continue;
            }

            if ('method' === $type && \in_array('function', $this->configuration['elements'], true)) {
                $this->ensureSingleSpaceAtFunctionArgumentTypehint($functionsAnalyzer, $tokens, $index);

                // implicit continue;
            }
        }
    }

    /**
     * @return array<int, string>
     *
     * @phpstan-return array<int, 'method'|'property'>
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
            if (
                $token->isGivenKind(T_FN)
                || ($token->isGivenKind(T_FUNCTION) && !isset($elements[$index]))
            ) {
                $elements[$index] = 'method';
            }
        }

        return $elements;
    }

    private function ensureSingleSpaceAtFunctionArgumentTypehint(FunctionsAnalyzer $functionsAnalyzer, Tokens $tokens, int $index): void
    {
        foreach (array_reverse($functionsAnalyzer->getFunctionArguments($tokens, $index)) as $argumentInfo) {
            $argumentType = $argumentInfo->getTypeAnalysis();

            if (null === $argumentType) {
                continue;
            }

            $tokens->ensureWhitespaceAtIndex($argumentType->getEndIndex() + 1, 0, ' ');
        }
    }

    private function ensureSingleSpaceAtPropertyTypehint(Tokens $tokens, int $index): void
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

        if (null === $propertyType) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($propertyType->getEndIndex() + 1, 0, ' ');
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
}
