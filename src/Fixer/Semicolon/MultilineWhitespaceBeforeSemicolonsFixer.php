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

namespace PhpCsFixer\Fixer\Semicolon;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Egidijus Girčys <e.gircys@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
final class MultilineWhitespaceBeforeSemicolonsFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    const STRATEGY_NO_MULTI_LINE = 'no_multi_line';

    /**
     * @internal
     */
    const STRATEGY_NEW_LINE_FOR_CHAINED_CALLS = 'new_line_for_chained_calls';

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(';');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.',
            [
                new CodeSample(
                    '<?php
function foo () {
    return 1 + 2
        ;
}
'
                ),
                new CodeSample(
                    '<?php
                        $this->method1()
                            ->method2()
                            ->method(3);
                    ?>
',
                    ['strategy' => self::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(
                    'strategy',
                    'Forbid multi-line whitespace or move the semicolon to the new line for chained calls.'
            ))
                ->setAllowedValues([self::STRATEGY_NO_MULTI_LINE, self::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS])
                ->setDefault(self::STRATEGY_NO_MULTI_LINE)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        if (self::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS === $this->configuration['strategy']) {
            return $this->applyChainedCallsFix($tokens);
        }

        if (self::STRATEGY_NO_MULTI_LINE === $this->configuration['strategy']) {
            return $this->applyNoMultiLineFix($tokens);
        }
    }

    /**
     * @param Tokens $tokens
     */
    protected function applyNoMultiLineFix(Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        foreach ($tokens as $index => $token) {
            if (!$token->equals(';')) {
                continue;
            }

            $previousIndex = $index - 1;
            $previous = $tokens[$previousIndex];
            if (!$previous->isWhitespace() || false === strpos($previous->getContent(), "\n")) {
                continue;
            }

            $content = $previous->getContent();
            if (("\n" === $content[0] || "\r" === $content[0]) && $tokens[$index - 2]->isComment()) {
                $tokens[$previousIndex] = new Token([$previous->getId(), $lineEnding]);
            } else {
                $tokens->clearAt($previousIndex);
            }
        }
    }

    /**
     * @param Tokens $tokens
     */
    protected function applyChainedCallsFix(Tokens $tokens)
    {
        for ($index = count($tokens) - 1; $index >= 0; --$index) {
            // continue if token is not a semicolon
            if (!$tokens[$index]->equals(';')) {
                continue;
            }

            // get the indent of the chained call, null in case it's not a chained call
            $indent = $this->getIndentation($index - 1, $tokens);

            if (null === $indent) {
                continue;
            }

            // unset semicolon
            $tokens->clearAt($index);

            // find the line ending token index after the semicolon
            $index = $this->getNewLineIndex($index, $tokens);

            // line ending string of the last method call
            $lineEnding = $this->whitespacesConfig->getLineEnding();

            // appended new line to the last method call
            $newline = new Token([T_WHITESPACE, $lineEnding.$indent]);

            // insert the new line with indented semicolon
            $tokens->insertAt($index, [$newline, new Token(';')]);
        }
    }

    /**
     * Find the index for the new line. Return the given index when there's no new line.
     *
     * @param int    $index
     * @param Tokens $tokens
     *
     * @return int
     */
    private function getNewLineIndex($index, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($index, $count = count($tokens); $index < $count; ++$index) {
            if (false !== strstr($tokens[$index]->getContent(), $lineEnding)) {
                return $index;
            }
        }

        return $index;
    }

    /**
     * Find the indentation of the chained call at $index.
     *
     * @param int    $index
     * @param Tokens $tokens
     *
     * @return null|string
     */
    private function getIndentation($index, Tokens $tokens)
    {
        // semicolon followed by a closing bracket?
        if (!$tokens[$index]->equals(')')) {
            return null;
        }

        // find opening bracket
        $openingBrackets = 1;
        for (--$index; $index > 0; --$index) {
            if ($tokens[$index]->equals(')')) {
                ++$openingBrackets;

                continue;
            }

            if ($tokens[$index]->equals('(')) {
                if (1 === $openingBrackets) {
                    break;
                }
                --$openingBrackets;
            }
        }

        // method name
        if (!$tokens[--$index]->isGivenKind(T_STRING)) {
            return null;
        }

        // ->
        if (!$tokens[--$index]->isGivenKind(T_OBJECT_OPERATOR)) {
            return null;
        }

        // white space
        if (!$tokens[--$index]->isGivenKind(T_WHITESPACE)) {
            return null;
        }

        $closingBrackets = 0;
        for ($index; $index >= 0; --$index) {
            if ($tokens[$index]->equals(')')) {
                ++$closingBrackets;
            }

            if ($tokens[$index]->equals('(')) {
                --$closingBrackets;
            }

            // must be the variable of the first call in the chain
            if ($tokens[$index]->isGivenKind(T_VARIABLE) && 0 === $closingBrackets) {
                if ($tokens[--$index]->isGivenKind(T_WHITESPACE)) {
                    return $this->getIndentAt($tokens, $index);
                }
            }
        }

        return null;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  index of the indentation token
     *
     * @return null|string
     */
    private function getIndentAt(Tokens $tokens, $index)
    {
        $content = '';
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // find line ending token
        for ($index; $index > 0; --$index) {
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

        if (1 === preg_match('/\R{1}([ \t]*)$/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
