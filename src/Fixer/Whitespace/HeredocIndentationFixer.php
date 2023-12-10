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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gregor Harlan
 */
final class HeredocIndentationFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Heredoc/nowdoc content must be properly indented.',
            [
                new CodeSample(
                    <<<'SAMPLE'
                        <?php
                            $heredoc = <<<EOD
                        abc
                            def
                        EOD;

                            $nowdoc = <<<'EOD'
                        abc
                            def
                        EOD;

                        SAMPLE
                ),
                new CodeSample(
                    <<<'SAMPLE'
                        <?php
                            $nowdoc = <<<'EOD'
                        abc
                            def
                        EOD;

                        SAMPLE
                    ,
                    ['indentation' => 'same_as_start']
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after BracesFixer, StatementIndentationFixer.
     */
    public function getPriority(): int
    {
        return -26;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_START_HEREDOC);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('indentation', 'Whether the indentation should be the same as in the start token line or one level more.'))
                ->setAllowedValues(['start_plus_one', 'same_as_start'])
                ->setDefault('start_plus_one')
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(T_END_HEREDOC)) {
                continue;
            }

            $end = $index;
            $index = $tokens->getPrevTokenOfKind($index, [[T_START_HEREDOC]]);

            $this->fixIndentation($tokens, $index, $end);
        }
    }

    private function fixIndentation(Tokens $tokens, int $start, int $end): void
    {
        $indent = WhitespacesAnalyzer::detectIndent($tokens, $start);

        if ('start_plus_one' === $this->configuration['indentation']) {
            $indent .= $this->whitespacesConfig->getIndent();
        }

        Preg::match('/^\h*/', $tokens[$end]->getContent(), $matches);
        $currentIndent = $matches[0];
        $currentIndentLength = \strlen($currentIndent);

        $content = $indent.substr($tokens[$end]->getContent(), $currentIndentLength);
        $tokens[$end] = new Token([T_END_HEREDOC, $content]);

        if ($end === $start + 1) {
            return;
        }

        for ($index = $end - 1, $last = true; $index > $start; --$index, $last = false) {
            if (!$tokens[$index]->isGivenKind([T_ENCAPSED_AND_WHITESPACE, T_WHITESPACE])) {
                continue;
            }

            $content = $tokens[$index]->getContent();

            if ('' !== $currentIndent) {
                $content = Preg::replace('/(?<=\v)(?!'.$currentIndent.')\h+/', '', $content);
            }

            $regexEnd = $last && !$currentIndent ? '(?!\v|$)' : '(?!\v)';
            $content = Preg::replace('/(?<=\v)'.$currentIndent.$regexEnd.'/', $indent, $content);

            $tokens[$index] = new Token([$tokens[$index]->getId(), $content]);
        }

        ++$index;

        if (!$tokens[$index]->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
            $tokens->insertAt($index, new Token([T_ENCAPSED_AND_WHITESPACE, $indent]));

            return;
        }

        $content = $tokens[$index]->getContent();

        if (!\in_array($content[0], ["\r", "\n"], true) && (!$currentIndent || str_starts_with($content, $currentIndent))) {
            $content = $indent.substr($content, $currentIndentLength);
        } elseif ($currentIndent) {
            $content = Preg::replace('/^(?!'.$currentIndent.')\h+/', '', $content);
        }

        $tokens[$index] = new Token([T_ENCAPSED_AND_WHITESPACE, $content]);
    }
}
