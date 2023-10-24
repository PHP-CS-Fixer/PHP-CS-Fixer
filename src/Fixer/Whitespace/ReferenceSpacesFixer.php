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
 * @author Jesper Skytte <jesper@skytte.it>
 */
final class ReferenceSpacesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const TYPE_ASSIGNMENT = 'assignment';

    /**
     * @internal
     */
    public const TYPE_FUNCTION_SIGNATURE = 'function_signature';

    /**
     * @internal
     */
    public const TYPE_ANONYMOUS_FUNCTION_USE_BLOCK = 'anonymous_function_use_block';

    /**
     * @internal
     */
    public const BY_ASSIGN = 'by_assign';

    /**
     * @internal
     */
    public const BY_REFERENCE = 'by_reference';

    /**
     * @internal
     */
    public const SINGLE_SPACE = 'single_space';

    private array $relevantTokens = [];

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->relevantTokens = \PHP_VERSION_ID >= 80100
            ? [T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG, T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG, '&']
            : ['&'];
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Reference operator should be surrounded by space as defined.',
            [
                new CodeSample(
                    '<?php $foo = & $bar;
'
                ),
                new CodeSample(
                    '<?php $foo = & $bar;
',
                    ['assignment' => self::BY_ASSIGN]
                ),
                new CodeSample(
                    '<?php $foo = & $bar;
',
                    ['assignment' => self::BY_REFERENCE]
                ),
                new CodeSample(
                    '<?php $foo =&$bar;
',
                    ['assignment' => self::SINGLE_SPACE]
                ),
                new CodeSample(
                    '<?php function foo(& $bar) {}
',
                    ['function_signature' => self::BY_REFERENCE]
                ),
                new CodeSample(
                    '<?php function foo(&$bar) {}
',
                    ['function_signature' => self::SINGLE_SPACE]
                ),
                new CodeSample(
                    '<?php $foo = function () use (& $bar) {};
',
                    ['anonymous_function_use_block' => self::BY_REFERENCE]
                ),
                new CodeSample(
                    '<?php $foo = function () use (&$bar) {};
',
                    ['anonymous_function_use_block' => self::SINGLE_SPACE]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after BinaryOperatorSpacesFixer
     */
    public function getPriority(): int
    {
        return -33;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound($this->relevantTokens);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('assignment', 'Default fix strategy for reference operator in assignments.'))
                ->setDefault(self::BY_REFERENCE)
                ->setAllowedValues([self::BY_REFERENCE, self::SINGLE_SPACE, self::BY_ASSIGN, false])
                ->getOption(),
            (new FixerOptionBuilder(
                'function_signature',
                'Default fix strategy for reference operator in function signatures.'
            ))
                ->setDefault(self::BY_REFERENCE)
                ->setAllowedValues([self::BY_REFERENCE, self::SINGLE_SPACE, false])
                ->getOption(),
            (new FixerOptionBuilder(
                'anonymous_function_use_block',
                'Default fix strategy for reference operator in anonymous function\'s use blocks.'
            ))
                ->setDefault(self::BY_REFERENCE)
                ->setAllowedValues([self::BY_REFERENCE, self::SINGLE_SPACE, false])
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if ('&' !== $tokens[$index]->getContent()) {
                continue;
            }

            $this->fixWhiteSpaceAfterOperator($tokens, $index);
            $this->fixWhiteSpaceBeforeOperator($tokens, $index);
        }
    }

    /**
     * Fix whitespace before the reference operator.
     *
     * @param Tokens $tokens
     * @param int $index
     * @return void
     */
    private function fixWhiteSpaceBeforeOperator(Tokens $tokens, int $index): void
    {
        // Only on assign by reference is it possible to fix space before
        $prevToken = $tokens->getPrevMeaningfulToken($index);
        if ('=' !== $tokens[$prevToken]->getContent()) {
            return;
        }

        $strategy = $this->configuration[self::TYPE_ASSIGNMENT];
        if (false === $strategy) {
            return;
        }

        $isWhitespace = $tokens[$index - 1]->isWhitespace();

        if (
            !$isWhitespace
            && \in_array($strategy, [self::BY_REFERENCE, self::SINGLE_SPACE], true)
        ) {
            $tokens->insertAt($index, new Token([T_WHITESPACE, ' ']));
        } elseif ($isWhitespace) {
            if (self::BY_ASSIGN === $strategy) {
                $tokens->clearAt($index - 1);
            } elseif (' ' !== $tokens[$index - 1]->getContent()) {
                $tokens[$index - 1] = new Token([T_WHITESPACE, ' ']);
            }
        }
    }

    /**
     * Fix whitespace after the reference operator.
     *
     * @param Tokens $tokens
     * @param int $index
     * @return void
     */
    private function fixWhiteSpaceAfterOperator(Tokens $tokens, int $index): void
    {
        $type = '';
        // Only on assign by reference is it possible to fix space before
        for ($search = $index - 1; $search > 0; --$search) {
            if (
                $tokens[$search]->isGivenKind([T_FUNCTION, T_USE, CT::T_USE_LAMBDA])
                || '=' === $tokens[$search]->getContent()
            ) {
                $type = $this->getReferenceType($tokens[$search]);
                break;
            }
        }

        $strategy = $this->configuration[$type];
        if (($strategy ?? false) === false) {
            return;
        }

        $isWhitespace = $tokens[$index + 1]->isWhitespace();
        if (
            !$isWhitespace
            && \in_array($strategy, [self::BY_ASSIGN, self::SINGLE_SPACE], true)
        ) {
            $tokens->insertAt($index + 1, new Token([T_WHITESPACE, ' ']));
        } elseif ($isWhitespace) {
            if (self::BY_REFERENCE === $strategy) {
                $tokens->clearAt($index + 1);
            } elseif (' ' !== $tokens[$index + 1]->getContent()) {
                $tokens[$index + 1] = new Token([T_WHITESPACE, ' ']);
            }
        }
    }

    /**
     * Determines the reference type based on the given token.
     *
     * @param Token $token The token to determine the reference type for.
     *
     * @return string The reference type.
     */
    private function getReferenceType(Token $token): string
    {
        if ($token->isGivenKind(T_FUNCTION)) {
            return self::TYPE_FUNCTION_SIGNATURE;
        }

        if ($token->isGivenKind([T_USE, CT::T_USE_LAMBDA])) {
            return self::TYPE_ANONYMOUS_FUNCTION_USE_BLOCK;
        }

        return self::TYPE_ASSIGNMENT;
    }
}
