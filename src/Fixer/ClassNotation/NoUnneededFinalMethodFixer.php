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
            'A `final` class must not have `final` methods and `private` methods must not be `final`.',
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
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FINAL]);
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
        $tokensCount = \count($tokens);
        for ($index = 0; $index < $tokensCount; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS)) {
                continue;
            }

            $classOpen = $tokens->getNextTokenOfKind($index, ['{']);
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            $classIsFinal = $prevToken->isGivenKind(T_FINAL);

            $this->fixClass($tokens, $classOpen, $classIsFinal);
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

    private function fixClass(Tokens $tokens, int $classOpenIndex, bool $classIsFinal): void
    {
        $tokensCount = \count($tokens);

        for ($index = $classOpenIndex + 1; $index < $tokensCount; ++$index) {
            // Class end
            if ($tokens[$index]->equals('}')) {
                return;
            }

            // Skip method content
            if ($tokens[$index]->equals('{')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);

                continue;
            }

            if (!$tokens[$index]->isGivenKind(T_FINAL)) {
                continue;
            }

            if (!$classIsFinal && (!$this->isPrivateMethodOtherThanConstructor($tokens, $index, $classOpenIndex) || !$this->configuration['private_methods'])) {
                continue;
            }

            $tokens->clearAt($index);

            ++$index;

            if ($tokens[$index]->isWhitespace()) {
                $tokens->clearAt($index);
            }
        }
    }

    private function isPrivateMethodOtherThanConstructor(Tokens $tokens, int $index, int $classOpenIndex): bool
    {
        $index = max($classOpenIndex + 1, $tokens->getPrevTokenOfKind($index, [';', '{', '}']));
        $private = false;

        while (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
            if ($tokens[$index]->isGivenKind(T_PRIVATE)) {
                $private = true;
            }

            $index = $tokens->getNextMeaningfulToken($index);
        }

        return $private && '__construct' !== strtolower($tokens[$tokens->getNextMeaningfulToken($index)]->getContent());
    }
}
