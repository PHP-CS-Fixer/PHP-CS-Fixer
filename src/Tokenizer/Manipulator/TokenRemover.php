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

namespace PhpCsFixer\Tokenizer\Manipulator;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class TokenRemover
{
    /**
     * @var Tokens
     */
    private $tokens;

    public function __construct(Tokens $tokens)
    {
        $this->tokens = $tokens;
    }

    public function clearToken($index)
    {
        $previous = $this->tokens->getNonEmptySibling($index, -1);
        $next = $this->tokens->getNonEmptySibling($index, 1);

        // --- if nothing before the token check space after it ----------

        if (null === $previous) {
            // remove trailing space if any
            if (false === strpos($this->tokens[$index]->getContent(), "\n") && null !== $next && $this->tokens[$next]->isWhitespace()) {
                $linebreakAfterTokenStrIndex = strpos($this->tokens[$next]->getContent(), "\n");

                if (false === $linebreakAfterTokenStrIndex) {
                    if (null === $this->tokens->getNonEmptySibling($next, 1)) {
                        $this->tokens->clearTokenAndMergeSurroundingWhitespace($next);
                    }
                } else {
                    $newContent = substr($this->tokens[$next]->getContent(), $linebreakAfterTokenStrIndex + 1);

                    if ('' === $newContent || false === $newContent) { // false check for PHP5.6
                        $this->tokens->clearTokenAndMergeSurroundingWhitespace($next);
                    } else {
                        $this->tokens[$next] = new Token([T_WHITESPACE, $newContent]);
                    }
                }
            }

            $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

            return;
        }

        // --- check space before the token ------------------------------

        if (!$this->tokens[$previous]->isWhitespace() && !$this->tokens[$previous]->isGivenKind(T_OPEN_TAG)) {
            $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

            return; // there is more content on the same line before the token
        }

        $prePrevious = null;
        $linebreakBeforeTokenStrIndex = strrpos($this->tokens[$previous]->getContent(), "\n");

        if (false === $linebreakBeforeTokenStrIndex && $this->tokens[$previous]->isWhitespace()) {
            // test for open tag with line break followed by space without linebreak followed by token to remove
            $prePrevious = $this->tokens->getNonEmptySibling($previous, -1);

            if (null !== $prePrevious) {
                $linebreakBeforeTokenStrIndex = strpos($this->tokens[$prePrevious]->getContent(), "\n");
            }
        }

        if (false === $linebreakBeforeTokenStrIndex) {
            $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

            return; // there is more content on the same line before the token
        }

        // --- check space after the token -------------------------------

        $linebreakAfterTokenStrIndex = false;

        if (null !== $next) {
            if (!$this->tokens[$next]->isWhitespace()) {
                $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

                return; // more content on the same line after the token
            }

            $linebreakAfterTokenStrIndex = strpos($this->tokens[$next]->getContent(), "\n");

            if (false === $linebreakAfterTokenStrIndex && null !== $this->tokens->getNonEmptySibling($next, 1)) {
                $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

                return; // more content on the same line after the token (here trailing space that is not the last token)
            }
        }

        // --- clear line break before the token -------------------------

        if (null !== $prePrevious) {
            $this->tokens->clearTokenAndMergeSurroundingWhitespace($previous);

            // open tag -> space without line break -> token -> [nothing/trailing space with or without line break]
            if (null === $next || false === $linebreakAfterTokenStrIndex) {
                $this->tokens[$prePrevious] = new Token([T_OPEN_TAG, rtrim($this->tokens[$prePrevious]->getContent()).' ']);
            } else {
                // remove the line break from the next space token and not from the open tag
                $newContent = substr($this->tokens[$next]->getContent(), $linebreakAfterTokenStrIndex + 1);
                if ('' === $newContent || false === $newContent) { // false check for PHP5.6
                    $this->tokens->clearTokenAndMergeSurroundingWhitespace($next);
                } else {
                    $this->tokens[$next] = new Token([T_WHITESPACE, $newContent]);
                }

                $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

                return; // return because both before and after the token has been dealt with
            }
        } elseif ($this->tokens[$previous]->isGivenKind(T_OPEN_TAG)) {
            if (null === $next || false === $linebreakAfterTokenStrIndex) {
                $this->tokens[$previous] = new Token([T_OPEN_TAG, rtrim($this->tokens[$previous]->getContent()).' ']);
            } else {
                // remove the line break from the next space token and not from the open tag
                $newContent = substr($this->tokens[$next]->getContent(), $linebreakAfterTokenStrIndex + 1);

                if ('' === $newContent || false === $newContent) { // false check for PHP5.6
                    $this->tokens->clearTokenAndMergeSurroundingWhitespace($next);
                } else {
                    $this->tokens[$next] = new Token([T_WHITESPACE, $newContent]);
                }

                $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

                return; // return because both before and after the token has been dealt with
            }
        } else {
            $newContent = substr($this->tokens[$previous]->getContent(), 0, $linebreakBeforeTokenStrIndex);

            if ('' === $newContent) {
                $this->tokens->clearTokenAndMergeSurroundingWhitespace($previous);
            } else {
                $this->tokens[$previous] = new Token([T_WHITESPACE, $newContent]);
            }
        }

        // --- clear trailing space after token if any -------------------

        if (null === $next) {
            $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);

            return;
        }

        if (false === $linebreakAfterTokenStrIndex) {
            $this->tokens->clearTokenAndMergeSurroundingWhitespace($next);
        } else {
            $this->tokens[$next] = new Token([T_WHITESPACE, substr($this->tokens[$next]->getContent(), $linebreakAfterTokenStrIndex)]);
        }

        $this->tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
}
