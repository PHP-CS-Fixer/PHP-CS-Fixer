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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NewWithBracesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'All instances created with `new` keyword must (not) be followed by braces.',
            [
                new CodeSample("<?php\n\n\$x = new X;\n\$y = new class {};\n"),
                new CodeSample(
                    "<?php\n\n\$y = new class() {};\n",
                    ['anonymous_class' => false]
                ),
                new CodeSample(
                    "<?php\n\n\$x = new X();\n",
                    ['named_class' => false]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before ClassDefinitionFixer.
     */
    public function getPriority(): int
    {
        return 37;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_NEW);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        static $nextTokenKinds = null;

        if (null === $nextTokenKinds) {
            $nextTokenKinds = [
                '?',
                ';',
                ',',
                '(',
                ')',
                '[',
                ']',
                ':',
                '<',
                '>',
                '+',
                '-',
                '*',
                '/',
                '%',
                '&',
                '^',
                '|',
                [T_CLASS],
                [T_IS_SMALLER_OR_EQUAL],
                [T_IS_GREATER_OR_EQUAL],
                [T_IS_EQUAL],
                [T_IS_NOT_EQUAL],
                [T_IS_IDENTICAL],
                [T_IS_NOT_IDENTICAL],
                [T_CLOSE_TAG],
                [T_LOGICAL_AND],
                [T_LOGICAL_OR],
                [T_LOGICAL_XOR],
                [T_BOOLEAN_AND],
                [T_BOOLEAN_OR],
                [T_SL],
                [T_SR],
                [T_INSTANCEOF],
                [T_AS],
                [T_DOUBLE_ARROW],
                [T_POW],
                [T_SPACESHIP],
                [CT::T_ARRAY_SQUARE_BRACE_OPEN],
                [CT::T_ARRAY_SQUARE_BRACE_CLOSE],
                [CT::T_BRACE_CLASS_INSTANTIATION_OPEN],
                [CT::T_BRACE_CLASS_INSTANTIATION_CLOSE],
            ];

            if (\defined('T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG')) { // @TODO: drop condition when PHP 8.1+ is required
                $nextTokenKinds[] = [T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG];
                $nextTokenKinds[] = [T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG];
            }
        }

        for ($index = $tokens->count() - 3; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_NEW)) {
                continue;
            }

            $nextIndex = $tokens->getNextTokenOfKind($index, $nextTokenKinds);

            // new anonymous class definition
            if ($tokens[$nextIndex]->isGivenKind(T_CLASS)) {
                $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);

                if ($this->configuration['anonymous_class']) {
                    $this->ensureBracesAt($tokens, $nextIndex);
                } else {
                    $this->ensureNoBracesAt($tokens, $nextIndex);
                }

                continue;
            }

            // entrance into array index syntax - need to look for exit

            while ($tokens[$nextIndex]->equals('[') || $tokens[$nextIndex]->isGivenKind(CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN)) {
                $nextIndex = $tokens->findBlockEnd(Tokens::detectBlockType($tokens[$nextIndex])['type'], $nextIndex);
                $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
            }

            if ($this->configuration['named_class']) {
                $this->ensureBracesAt($tokens, $nextIndex);
            } else {
                $this->ensureNoBracesAt($tokens, $nextIndex);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('named_class', 'Whether named classes should be followed by parentheses.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder('anonymous_class', 'Whether anonymous classes should be followed by parentheses.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    private function ensureBracesAt(Tokens $tokens, int $index): void
    {
        $token = $tokens[$index];

        if (!$token->equals('(') && !$token->isObjectOperator()) {
            $tokens->insertAt(
                $tokens->getPrevMeaningfulToken($index) + 1,
                [new Token('('), new Token(')')]
            );
        }
    }

    private function ensureNoBracesAt(Tokens $tokens, int $index): void
    {
        if (!$tokens[$index]->equals('(')) {
            return;
        }

        $closingIndex = $tokens->getNextMeaningfulToken($index);

        // constructor has arguments - braces can not be removed
        if (!$tokens[$closingIndex]->equals(')')) {
            return;
        }

        $tokens->clearTokenAndMergeSurroundingWhitespace($closingIndex);
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
}
