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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Egidijus Girčys <e.gircys@gmail.com>
 */
final class SemicolonOnNewLineForChainedCallFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
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
            'Semicolon must be on the new line for chained calls.',
            [
                new CodeSample('
                    <?php
                        $this->method1()
                            ->method2()
                            ->method(3);
                    ?>
'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run after the NoSinglelineWhitespaceBeforeSemicolonsFixer
        return 10;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 1, $count = count($tokens); $index < $count; ++$index) {
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
            $lineEnding = $this->getLineEnding($index, $tokens);

            // appended new line to the last method call
            $newline = new Token([T_WHITESPACE, $lineEnding.$indent]);

            // insert the new line with indented semicolon
            $tokens->insertAt($index, [$newline, new Token(';')]);
        }
    }

    /**
     * Get the line ending string of the last method call.
     *
     * @param int    $index
     * @param Tokens $tokens
     *
     * @return bool|string
     */
    private function getLineEnding($index, Tokens $tokens)
    {
        // no line end, i.e. ends with a semicolon?
        if (!array_key_exists($index, $tokens)) {
            return $this->whitespacesConfig->getLineEnding();
        }

        $lineEnding = $tokens[$index]->getContent();

        return substr($lineEnding, 0, strpos(
            $lineEnding,
            $this->whitespacesConfig->getLineEnding()
        ) + 1);
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
                --$index;
                if ($tokens[$index]->isGivenKind(T_WHITESPACE)) {
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
