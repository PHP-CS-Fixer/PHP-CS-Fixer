<?php

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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Gwalchmei <darainas2@gmail.com>*
 */
final class TernaryOperatorMultilineFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    const STRATEGY_NO_MULTI_LINE = 'no_multi_line';
    const STRATEGY_OPERATORS_AT_END = 'operators_at_end';
    const STRATEGY_OPERATORS_AT_BEGINNING = 'operators_at_beginning';

    public function getDefinition()
    {
        return new FixerDefinition(
            'Standardize multi-lines ternary operators.',
            [new CodeSample("<?php \$a = \$a ?\n1\n: 0;")]
        );
    }

    /**
     * {@inheritdoc}
     * To be executed before \PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer.
     */
    public function getPriority()
    {
        return 1;
    }

    public function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(
                'strategy',
                'Forbid multi-line, force operators at the end of lines or force operators at the beginning of lines'
            ))
                ->setAllowedValues([self::STRATEGY_NO_MULTI_LINE, self::STRATEGY_OPERATORS_AT_BEGINNING, self::STRATEGY_OPERATORS_AT_END])
                ->setDefault(self::STRATEGY_NO_MULTI_LINE)
                ->getOption(),
            (new FixerOptionBuilder(
                'ignore-single-line',
                'Should ignore the single line operators'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(['?', ':']);
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $ternaryLevel = 0; // used to know if a token `:` is found after a `?` token

        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny(['?', ':'])) {
                continue;
            }

            if ($this->configuration['ignore-single-line'] && $token->equals('?') && 0 === $ternaryLevel && $this->isSingleLine($tokens, $index)) {
                continue;
            }

            if (($token->equals('?') && !$tokens[$index + 1]->equals(':') && ++$ternaryLevel)
                || ($ternaryLevel > 0 && $token->equals(':') && !$tokens[$index - 1]->equals('?') && --$ternaryLevel >= 0)) {
                if (self::STRATEGY_NO_MULTI_LINE === $this->configuration['strategy']) {
                    $this->applyNoMultilineFix($tokens, $index);
                } elseif (self::STRATEGY_OPERATORS_AT_END === $this->configuration['strategy']) {
                    $this->applyOperatorsAtEndFix($tokens, $index);
                } elseif (self::STRATEGY_OPERATORS_AT_BEGINNING === $this->configuration['strategy']) {
                    $this->applyOperatorsAtBeginningFix($tokens, $index);
                }
            }
        }
    }

    private function applyNoMultilineFix(Tokens $tokens, $index)
    {
        $this->clearBreakLineAtIndex($tokens, $index - 1);
        $this->clearBreakLineAtIndex($tokens, $index + 1);
    }

    private function applyOperatorsAtEndFix(Tokens $tokens, $index)
    {
        $tokens->ensureWhitespaceAtIndex($index + 1, 0, $this->whitespacesConfig->getLineEnding().$this->getIndentAt($tokens, $tokens->getPrevMeaningfulToken($index)).($tokens[$index]->equals('?') ? $this->whitespacesConfig->getIndent() : ''));
        $this->clearBreakLineAtIndex($tokens, $index - 1);
    }

    private function applyOperatorsAtBeginningFix(Tokens $tokens, $index)
    {
        $tokens->ensureWhitespaceAtIndex($index - 1, 0, $this->whitespacesConfig->getLineEnding().$this->getIndentAt($tokens, $tokens->getPrevMeaningfulToken($index)).($tokens[$index]->equals('?') ? $this->whitespacesConfig->getIndent() : ''));
        $this->clearBreakLineAtIndex($tokens, $index + 1);
    }

    private function clearBreakLineAtIndex(Tokens $tokens, $index)
    {
        $token = $tokens[$index];
        if ($token->isWhitespace() && false !== strpos($token->getContent(), "\n")) {
            $tokens->offsetSet($index, new Token([T_WHITESPACE, ' ']));
        } else {
            $tokens->ensureWhitespaceAtIndex($index, 1, ' ');
        }
    }

    /**
     * Currently I use the \PhpCsFixer\Tokenizer\Tokens::getPrevMeaningfulToken method to find a reference token
     * from which I can get the indentation to apply to the fixed new line.
     *
     * The goal of this method is to find the start of a ternary operator from which I can get the indentation
     *
     * @deprecated Does not work for nested ternary operators
     *
     * @param mixed $index
     */
    private function getStartOfOperator(Tokens $tokens, $index)
    {
        $startingKinds = [T_ECHO, T_YIELD, T_YIELD_FROM, T_RETURN];
        $closingTags = 0;
        for (; $index >= 0; --$index) {
            $token = $tokens[$index];
            if ($token->equalsAny(['}', ')'])) {
                ++$closingTags;
            } elseif ($token->equalsAny(['{', '('])) {
                --$closingTags;
            }
            if ($closingTags > 0) {
                continue;
            }

            if ($token->equals(',')) {
                return $tokens->getNextMeaningfulToken($index);
            }

            if ($token->isGivenKind($startingKinds)) {
                return $index;
            }
            if (!$token->isGivenKind(T_STRING) && false !== strstr($token->getContent(), '=')) {
                $startingKinds[] = T_VARIABLE;
            }
        }

        return $index;
    }

    /**
     * @param int $index
     *
     * @return null|string
     */
    private function getIndentAt(Tokens $tokens, $index)
    {
        $content = '';
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // find line ending token
        for (; $index > 0; --$index) {
            if (false !== strstr($tokens[$index]->getContent(), $lineEnding)) {
                break;
            }
        }

        if ($tokens[$index]->isWhitespace()) {
            $content = $tokens[$index]->getContent();
            --$index;
        }

        if ($tokens[$index]->isGivenKind(T_OPEN_TAG)) {
            $content = $tokens[$index]->getContent().$content;
        }

        if (1 === Preg::match('/\R{1}([ \t]*)$/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function isSingleLine(Tokens $tokens, $index)
    {
        $openingTags = 0;
        for (; $index < \count($tokens); ++$index) {
            $token = $tokens[$index];
            if ($token->isWhitespace() && false !== strstr($token->getContent(), "\n")) {
                return false;
            }

            if ($token->equalsAny(['{', '(', '['])) {
                ++$openingTags;
            } elseif ($token->equalsAny([')', '}', ']'])) {
                --$openingTags;
            }

            if ($openingTags > 0) {
                continue;
            }

            if ($token->equals(';')) {
                return true;
            }
        }

        return true;
    }
}
