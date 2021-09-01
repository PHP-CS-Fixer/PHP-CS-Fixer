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

namespace PhpCsFixer\Fixer\ListNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
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

final class ListSyntaxFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var null|int
     */
    private $candidateTokenKind;

    /**
     * Use 'syntax' => 'long'|'short'.
     *
     * @param array<string, string> $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->candidateTokenKind = 'long' === $this->configuration['syntax'] ? CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN : T_LIST;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.',
            [
                new CodeSample(
                    "<?php\nlist(\$sample) = \$array;\n"
                ),
                new CodeSample(
                    "<?php\n[\$sample] = \$array;\n",
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
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if ($tokens[$index]->isGivenKind($this->candidateTokenKind)) {
                if (T_LIST === $this->candidateTokenKind) {
                    $this->fixToShortSyntax($tokens, $index);
                } else {
                    $this->fixToLongSyntax($tokens, $index);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('syntax', 'Whether to use the `long` or `short` `list` syntax.'))
                ->setAllowedValues(['long', 'short'])
                ->setDefault('short')
                ->getOption(),
        ]);
    }

    private function fixToLongSyntax(Tokens $tokens, int $index): void
    {
        static $typesOfInterest = [
            [CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE],
            '[', // [CT::T_ARRAY_SQUARE_BRACE_OPEN],
        ];

        $closeIndex = $tokens->getNextTokenOfKind($index, $typesOfInterest);
        if (!$tokens[$closeIndex]->isGivenKind(CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE)) {
            return;
        }

        $tokens[$index] = new Token('(');
        $tokens[$closeIndex] = new Token(')');
        $tokens->insertAt($index, new Token([T_LIST, 'list']));
    }

    private function fixToShortSyntax(Tokens $tokens, int $index): void
    {
        $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

        $tokens[$openIndex] = new Token([CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN, '[']);
        $tokens[$closeIndex] = new Token([CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE, ']']);

        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
}
