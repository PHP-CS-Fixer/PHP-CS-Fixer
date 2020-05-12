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

namespace PhpCsFixer\Fixer\ArrayNotation;

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
 * @author Gregor Harlan <gharlan@web.de>
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ArraySyntaxFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var null|int
     */
    private $candidateTokenKind;

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

        $this->resolveCandidateTokenKind();
        $this->resolveFixCallback();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHP arrays should be declared using the configured syntax.',
            [
                new CodeSample(
                    "<?php\narray(1,2);\n"
                ),
                new CodeSample(
                    "<?php\n[1,2];\n",
                    ['syntax' => 'long']
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, TernaryOperatorSpacesFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound($this->candidateTokenKind);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $callback = $this->fixCallback;

        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if ($tokens[$index]->isGivenKind($this->candidateTokenKind)) {
                $this->{$callback}($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('syntax', 'Whether to use the `long` or `short` array syntax.'))
                ->setAllowedValues(['long', 'short'])
                ->setDefault('short')
                ->getOption(),
        ]);
    }

    private function fixToLongArraySyntax(Tokens $tokens, int $index): void
    {
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);

        $tokens[$index] = new Token('(');
        $tokens[$closeIndex] = new Token(')');

        $tokens->insertAt($index, new Token([T_ARRAY, 'array']));
    }

    private function fixToShortArraySyntax(Tokens $tokens, int $index): void
    {
        $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

        $tokens[$openIndex] = new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']);
        $tokens[$closeIndex] = new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']);

        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }

    private function resolveFixCallback(): void
    {
        $this->fixCallback = sprintf('fixTo%sArraySyntax', ucfirst($this->configuration['syntax']));
    }

    private function resolveCandidateTokenKind(): void
    {
        $this->candidateTokenKind = 'long' === $this->configuration['syntax'] ? CT::T_ARRAY_SQUARE_BRACE_OPEN : T_ARRAY;
    }
}
