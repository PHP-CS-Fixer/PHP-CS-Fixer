<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.4, ¶4.6.
 *
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
final class MethodArgumentSpaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.';
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($token->equals('(') && !$tokens[$index - 1]->isGivenKind(T_ARRAY)) {
                $this->fixFunction($tokens, $index);
            }
        }
    }

    /**
     * Fix arguments spacing for given function.
     *
     * @param Tokens $tokens             Tokens to handle
     * @param int    $startFunctionIndex Start parenthesis position
     */
    private function fixFunction(Tokens $tokens, $startFunctionIndex)
    {
        // detect if the arguments should be formatted over multiple lines
        $multiLine = false;
        $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);
        for ($i = $endFunctionIndex - 1; $i > $startFunctionIndex; --$i) {
            if ($tokens[$i]->isWhitespace() && false !== strpos($tokens[$i]->getContent(), "\n")) {
                $multiLine = true;
                break;
            }
        }

        // start by correcting from the end, we loop backwards later on
        if ($multiLine) {
            // set or add linebreak after the last argument
            if ($tokens[$endFunctionIndex - 1]->isWhitespace()) {
                if (false === strpos($tokens[$endFunctionIndex - 1]->getContent(), "\n")) {
                    $tokens[$endFunctionIndex - 1]->setContent("\n");
                }
            } else {
                $tokens->insertAt($endFunctionIndex, new Token(array(T_WHITESPACE, "\n")));
                ++$endFunctionIndex;
            }
        } elseif ($tokens[$endFunctionIndex - 1]->isWhitespace() && $tokens[$endFunctionIndex - 2]->getContent() !== ',') {
            // if the last argument is not a trailing comma and the space between the last argument is not an linebreak, clean it up
            $tokens[$endFunctionIndex - 1]->clear();
        }

        // loop back to the start of the function and correct the arguments if needed
        for ($index = $endFunctionIndex - 1; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];

            if ($token->equals(')')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index, false);
                continue;
            }

            if ($token->isGivenKind(CT_ARRAY_SQUARE_BRACE_CLOSE)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index, false);
                continue;
            }

            if ($token->equals(',')) {
                $this->fixSpace($tokens, $index, $multiLine);
            }
        }

        // remove the spacing between the function opening and the next meaningful argument (like the first argument)
        if ($tokens[$startFunctionIndex + 1]->isWhitespace()) {
            if ($multiLine) {
                if (false === strpos($tokens[$startFunctionIndex + 1]->getContent(), "\n")) {
                    $tokens[$startFunctionIndex + 1]->setContent("\n");
                }
            } else {
                $tokens[$startFunctionIndex + 1]->clear();
            }
        } elseif ($multiLine) {
            // add a line break after the first if needed
            $tokens->insertAt($startFunctionIndex + 1, new Token(array(T_WHITESPACE, "\n")));
        }
    }

    /**
     * Method to insert space after comma and remove space before comma.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param bool   $multiLine
     */
    private function fixSpace(Tokens $tokens, $index, $multiLine)
    {
        // remove space before comma if exist
        if ($tokens[$index - 1]->isWhitespace()) {
            $prevIndex = $tokens->getPrevNonWhitespace($index - 1);

            if (!$tokens[$prevIndex]->equalsAny(array(',', array(T_END_HEREDOC)))) {
                $tokens[$index - 1]->clear();
            }
        }

        $nextToken = $tokens[$index + 1];

        // Two cases for fix space after comma (exclude multiline comments)
        //  1) multiple spaces after comma
        //  2) no space after comma
        if ($nextToken->isWhitespace()) {
            if ($this->isCommentLastLineToken($tokens, $index + 2)) {
                return;
            }

            if ($multiLine) {
                if (false === strpos($nextToken->getContent(), "\n")) {
                    $nextToken->setContent("\n");
                }
            } else {
                $newContent = ltrim($nextToken->getContent(), " \t");

                if ('' === $newContent) {
                    $newContent = ' ';
                }

                $nextToken->setContent($newContent);
            }

            return;
        }

        if (!$this->isCommentLastLineToken($tokens, $index + 1)) {
            if ($multiLine) {
                if ($tokens[$index + 1]->isWhitespace()) {
                    if (false === strpos($tokens[$index + 1]->getContent(), "\n")) {
                        $tokens[$index + 1]->setContent("\n");
                    }
                } else {
                    $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, "\n")));
                }
            } else {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
            }
        }
    }

    /**
     * Check if last item of current line is a comment.
     *
     * @param Tokens $tokens tokens to handle
     * @param int    $index  index of token
     *
     * @return bool
     */
    private function isCommentLastLineToken(Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isComment()) {
            return false;
        }

        $nextToken = $tokens[$index + 1];

        if (!$nextToken->isWhitespace()) {
            return false;
        }

        $content = $nextToken->getContent();

        return $content !== ltrim($content, "\r\n");
    }
}
