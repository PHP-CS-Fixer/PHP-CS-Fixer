<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class HeaderCommentFixer extends AbstractFixer
{
    private static $header = '';
    private static $headerComment = '';

    /**
     * Sets the desired header text.
     *
     * The given text will be trimmed and enclosed into a multiline comment.
     * If the text is empty, when a file get fixed, the header comment will be
     * erased.
     *
     * @param string $header
     */
    public static function setHeader($header)
    {
        self::$header = trim((string) $header);

        if (strlen(self::$header)) {
            self::$headerComment = self::encloseTextInComment(self::$header);
        } else {
            self::$headerComment = '';
        }
    }

    /**
     * @return string
     */
    public static function getHeader()
    {
        return self::$header;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if (!$tokens->isMonolithicPhp()) {
            return $content;
        }

        $this->removeHeaderComment($tokens);
        $insertionIndex = $this->findHeaderCommentInsertionIndex($tokens);
        $tokens->clearRange(1, $insertionIndex-1);
        $this->insertHeaderComment($tokens, $insertionIndex);

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Add, replace or remove header comment.';
    }

    /**
     * Encloses the given text in a comment block.
     *
     * @param string $header
     *
     * @return string
     */
    private static function encloseTextInComment($header)
    {
        $comment = "/*\n";
        $lines = explode("\n", str_replace("\r", '', $header));
        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line)."\n";
        }
        $comment .= " */\n";

        return $comment;
    }

    /**
     * Removes the header comment, if any.
     *
     * @param Tokens $tokens
     */
    private function removeHeaderComment(Tokens $tokens)
    {
        $index = $tokens->getNextNonWhitespace(0);
        if (null !== $index && $tokens[$index]->isGivenKind(T_COMMENT)) {
            $tokens[$index]->clear();
        }
    }

    /**
     * Finds the index where the header comment must be inserted.
     *
     * @param Tokens $tokens
     *
     * @return int
     */
    private function findHeaderCommentInsertionIndex(Tokens $tokens)
    {
        $index = $tokens->getNextNonWhitespace(0);

        if (null === $index) {
            //Empty file, insert at the end
            $index = $tokens->getSize();
        }

        return $index;
    }

    /**
     * Inserts the header comment at the given index.
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    private function insertHeaderComment(Tokens $tokens, $index)
    {
        $headCommentTokens = Tokens::fromArray(
            array(
                new Token(array(T_WHITESPACE, "\n")),
                new Token(array(T_COMMENT, self::$headerComment)),
                new Token(array(T_WHITESPACE, strlen(self::$headerComment) ? "\n" : '')),
            )
        );

        $tokens->insertAt($index, $headCommentTokens);
    }
}
