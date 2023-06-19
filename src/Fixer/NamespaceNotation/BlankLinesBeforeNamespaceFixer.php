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

namespace PhpCsFixer\Fixer\NamespaceNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Greg Korba <greg@codito.dev>
 */
final class BlankLinesBeforeNamespaceFixer extends AbstractFixer implements WhitespacesAwareFixerInterface, ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Controls blank lines before a namespace declaration.',
            [
                new CodeSample("<?php  namespace A {}\n"),
                new CodeSample("<?php  namespace A {}\n", ['min_line_breaks' => 1]),
                new CodeSample("<?php\n\ndeclare(strict_types=1);\n\n\n\nnamespace A{}\n", ['max_line_breaks' => 2]),
                new CodeSample("<?php\n\n/** Some comment */\nnamespace A{}\n", ['min_line_breaks' => 2]),
                new CodeSample("<?php\n\nnamespace A{}\n", ['min_line_breaks' => 0, 'max_line_breaks' => 0]),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_NAMESPACE);
    }

    /**
     * {@inheritdoc}
     *
     * Must run after BlankLineAfterOpeningTagFixer.
     */
    public function getPriority(): int
    {
        return 0;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('min_line_breaks', 'Minimum line breaks that should exist before namespace declaration.'))
                ->setAllowedTypes(['int'])
                ->setDefault(2)
                ->setNormalizer(function (Options $options, $value): int {
                    if ($value < 0) {
                        throw new InvalidFixerConfigurationException(
                            (new self())->getName(),
                            'Option `min_line_breaks` cannot be lower than 0.'
                        );
                    }

                    return $value;
                })
                ->getOption(),
            (new FixerOptionBuilder('max_line_breaks', 'Maximum line breaks that should exist before namespace declaration.'))
                ->setAllowedTypes(['int'])
                ->setDefault(2)
                ->setNormalizer(function (Options $options, $value): int {
                    if ($value < 0) {
                        throw new InvalidFixerConfigurationException(
                            (new self())->getName(),
                            'Option `max_line_breaks` cannot be lower than 0.'
                        );
                    }

                    if ($value < $options['min_line_breaks']) {
                        throw new InvalidFixerConfigurationException(
                            (new self())->getName(),
                            'Option `max_line_breaks` cannot have lower value than `min_line_breaks`.'
                        );
                    }

                    return $value;
                })
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_NAMESPACE)) {
                $this->fixLinesBeforeNamespace(
                    $tokens,
                    $index,
                    $this->configuration['min_line_breaks'],
                    $this->configuration['max_line_breaks']
                );
            }
        }
    }

    /**
     * Make sure # of line breaks prefixing namespace is within given range.
     *
     * @param int $expectedMin min. # of line breaks
     * @param int $expectedMax max. # of line breaks
     */
    protected function fixLinesBeforeNamespace(Tokens $tokens, int $index, int $expectedMin, int $expectedMax): void
    {
        // Let's determine the total numbers of new lines before the namespace
        // and the opening token
        $openingTokenIndex = null;
        $precedingNewlines = 0;
        $newlineInOpening = false;
        $openingToken = null;

        for ($i = 1; $i <= 2; ++$i) {
            if (isset($tokens[$index - $i])) {
                $token = $tokens[$index - $i];

                if ($token->isGivenKind(T_OPEN_TAG)) {
                    $openingToken = $token;
                    $openingTokenIndex = $index - $i;
                    $newlineInOpening = str_contains($token->getContent(), "\n");

                    if ($newlineInOpening) {
                        ++$precedingNewlines;
                    }

                    break;
                }

                if (false === $token->isGivenKind(T_WHITESPACE)) {
                    break;
                }

                $precedingNewlines += substr_count($token->getContent(), "\n");
            }
        }

        if ($precedingNewlines >= $expectedMin && $precedingNewlines <= $expectedMax) {
            return;
        }

        $previousIndex = $index - 1;
        $previous = $tokens[$previousIndex];

        if (0 === $expectedMax) {
            // Remove all the previous new lines
            if ($previous->isWhitespace()) {
                $tokens->clearAt($previousIndex);
            }

            // Remove new lines in opening token
            if ($newlineInOpening) {
                $tokens[$openingTokenIndex] = new Token([T_OPEN_TAG, rtrim($openingToken->getContent()).' ']);
            }

            return;
        }

        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // Allow only as many line breaks as configured:
        // - keep as-is when current preceding line breaks are within configured range
        // - use configured max line breaks if currently there is more preceding line breaks
        // - use configured min line breaks if currently there is less preceding line breaks
        $newlinesForWhitespaceToken = $precedingNewlines >= $expectedMax
            ? $expectedMax
            : max($precedingNewlines, $expectedMin);

        if (null !== $openingToken) {
            // Use the configured line ending for the PHP opening tag
            $content = rtrim($openingToken->getContent());
            $newContent = $content.$lineEnding;
            $tokens[$openingTokenIndex] = new Token([T_OPEN_TAG, $newContent]);
            --$newlinesForWhitespaceToken;
        }

        if (0 === $newlinesForWhitespaceToken) {
            // We have all the needed new lines in the opening tag
            if ($previous->isWhitespace()) {
                // Let's remove the previous token containing extra new lines
                $tokens->clearAt($previousIndex);
            }

            return;
        }

        if ($previous->isWhitespace()) {
            // Fix the previous whitespace token
            $tokens[$previousIndex] = new Token(
                [
                    T_WHITESPACE,
                    str_repeat($lineEnding, $newlinesForWhitespaceToken).substr(
                        $previous->getContent(),
                        strrpos($previous->getContent(), "\n") + 1
                    ),
                ]
            );
        } else {
            // Add a new whitespace token
            $tokens->insertAt($index, new Token([T_WHITESPACE, str_repeat($lineEnding, $newlinesForWhitespaceToken)]));
        }
    }
}
