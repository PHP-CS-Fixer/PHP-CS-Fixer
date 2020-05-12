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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ConcatSpaceFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var null|string
     */
    private $fixCallback;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        if ('one' === $this->configuration['spacing']) {
            $this->fixCallback = 'fixConcatenationToSingleSpace';
        } else {
            $this->fixCallback = 'fixConcatenationToNoSpace';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Concatenation should be spaced according configuration.',
            [
                new CodeSample(
                    "<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';\n"
                ),
                new CodeSample(
                    "<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';\n",
                    ['spacing' => 'none']
                ),
                new CodeSample(
                    "<?php\n\$foo = 'bar' . 3 . 'baz'.'qux';\n",
                    ['spacing' => 'one']
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after SingleLineThrowFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound('.');
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $callBack = $this->fixCallback;
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->equals('.')) {
                $this->{$callBack}($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('spacing', 'Spacing to apply around concatenation operator.'))
                ->setAllowedValues(['one', 'none'])
                ->setDefault('none')
                ->getOption(),
        ]);
    }

    /**
     * @param int $index index of concatenation '.' token
     */
    private function fixConcatenationToNoSpace(Tokens $tokens, int $index): void
    {
        $prevNonWhitespaceToken = $tokens[$tokens->getPrevNonWhitespace($index)];

        if (!$prevNonWhitespaceToken->isGivenKind([T_LNUMBER, T_COMMENT, T_DOC_COMMENT]) || str_starts_with($prevNonWhitespaceToken->getContent(), '/*')) {
            $tokens->removeLeadingWhitespace($index, " \t");
        }

        if (!$tokens[$tokens->getNextNonWhitespace($index)]->isGivenKind([T_LNUMBER, T_COMMENT, T_DOC_COMMENT])) {
            $tokens->removeTrailingWhitespace($index, " \t");
        }
    }

    /**
     * @param int $index index of concatenation '.' token
     */
    private function fixConcatenationToSingleSpace(Tokens $tokens, int $index): void
    {
        $this->fixWhiteSpaceAroundConcatToken($tokens, $index, 1);
        $this->fixWhiteSpaceAroundConcatToken($tokens, $index, -1);
    }

    /**
     * @param int $index  index of concatenation '.' token
     * @param int $offset 1 or -1
     */
    private function fixWhiteSpaceAroundConcatToken(Tokens $tokens, int $index, int $offset): void
    {
        $offsetIndex = $index + $offset;

        if (!$tokens[$offsetIndex]->isWhitespace()) {
            $tokens->insertAt($index + (1 === $offset ?: 0), new Token([T_WHITESPACE, ' ']));

            return;
        }

        if (str_contains($tokens[$offsetIndex]->getContent(), "\n")) {
            return;
        }

        if ($tokens[$index + $offset * 2]->isComment()) {
            return;
        }

        $tokens[$offsetIndex] = new Token([T_WHITESPACE, ' ']);
    }
}
