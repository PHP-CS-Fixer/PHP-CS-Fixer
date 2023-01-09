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
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class NoUnneededFinalMethodFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes `final` from methods where possible.',
            [
                new CodeSample(
                    '<?php
final class Foo
{
    final public function foo1() {}
    final protected function bar() {}
    final private function baz() {}
}

class Bar
{
    final private function bar1() {}
}
'
                ),
                new CodeSample(
                    '<?php
final class Foo
{
    final private function baz() {}
}

class Bar
{
    final private function bar1() {}
}
',
                    ['private_methods' => false]
                ),
            ],
            null,
            'Risky when child class overrides a `private` method.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        if (!$tokens->isAllTokenKindsFound([T_FINAL, T_FUNCTION])) {
            return false;
        }

        if (\defined('T_ENUM') && $tokens->isTokenKindFound(T_ENUM)) { // @TODO: drop condition when PHP 8.1+ is required
            return true;
        }

        return $tokens->isTokenKindFound(T_CLASS);
    }

    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->getMethods($tokens) as $element) {
            $index = $element['method_final_index'];

            if ($element['method_of_enum'] || $element['class_is_final']) {
                $this->clearFinal($tokens, $index);

                continue;
            }

            if (!$element['method_is_private'] || false === $this->configuration['private_methods'] || $element['method_is_constructor']) {
                continue;
            }

            $this->clearFinal($tokens, $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('private_methods', 'Private methods of non-`final` classes must not be declared `final`.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    private function getMethods(Tokens $tokens): \Generator
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $modifierKinds = [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_STATIC];

        $enums = [];
        $classesAreFinal = [];
        $elements = $tokensAnalyzer->getClassyElements();

        for (end($elements);; prev($elements)) {
            $index = key($elements);

            if (null === $index) {
                break;
            }

            $element = current($elements);

            if ('method' !== $element['type']) {
                continue; // not a method
            }

            $classIndex = $element['classIndex'];

            if (!\array_key_exists($classIndex, $enums)) {
                $enums[$classIndex] = \defined('T_ENUM') && $tokens[$classIndex]->isGivenKind(T_ENUM); // @TODO: drop condition when PHP 8.1+ is required
            }

            $element['method_final_index'] = null;
            $element['method_is_private'] = false;

            $previous = $index;

            do {
                $previous = $tokens->getPrevMeaningfulToken($previous);

                if ($tokens[$previous]->isGivenKind(T_PRIVATE)) {
                    $element['method_is_private'] = true;
                } elseif ($tokens[$previous]->isGivenKind(T_FINAL)) {
                    $element['method_final_index'] = $previous;
                }
            } while ($tokens[$previous]->isGivenKind($modifierKinds));

            if ($enums[$classIndex]) {
                $element['method_of_enum'] = true;

                yield $element;

                continue;
            }

            if (!\array_key_exists($classIndex, $classesAreFinal)) {
                $modifiers = $tokensAnalyzer->getClassyModifiers($classIndex);
                $classesAreFinal[$classIndex] = isset($modifiers['final']);
            }

            $element['method_of_enum'] = false;
            $element['class_is_final'] = $classesAreFinal[$classIndex];
            $element['method_is_constructor'] = '__construct' === strtolower($tokens[$tokens->getNextMeaningfulToken($index)]->getContent());

            yield $element;
        }
    }

    private function clearFinal(Tokens $tokens, ?int $index): void
    {
        if (null === $index) {
            return;
        }

        $tokens->clearAt($index);

        ++$index;

        if ($tokens[$index]->isWhitespace()) {
            $tokens->clearAt($index);
        }
    }
}
