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
    private static $useLeadingNewLine = true;
    private static $useDocBlockComment = false;

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
        self::$headerComment = '';

        if ('' !== self::$header) {
            self::$headerComment = self::encloseTextInComment(self::$header);
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
     * Sets the newline mode.
     *
     * The multiline comment will appear with a leading newline by default.
     * This setting will control whether the newline appears or not.
     *
     * @param bool $new Should the newline appear?
     */
    public static function setUseLeadingNewLine($new)
    {
        self::$useLeadingNewLine = (bool) $new;
    }

    /**
     * Sets the comment mode.
     *
     * The multiline comment is by-default rendered with /*. This setting
     * allows for the usage of docblock comments /**.
     *
     * @param bool $new Should the comment be a docblock comment?
     */
    public static function setUseDocBlockComment($new)
    {
        self::$useDocBlockComment = (bool) $new;
        if ('' !== self::$headerComment) {
            $leading = self::$useDocBlockComment ? '/**' : '/*';
            self::$headerComment = $leading.strstr(self::$headerComment, "\n");
        }
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
        $tokens->clearRange(1, $insertionIndex - 1);
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
        $comment = self::$useDocBlockComment ? "/**\n" : "/*\n";
        $lines = explode("\n", str_replace("\r", '', $header));
        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line)."\n";
        }
        $comment .= ' */';

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
        $token = self::$useDocBlockComment ? T_DOC_COMMENT : T_COMMENT;
        if (null !== $index && $tokens[$index]->isGivenKind($token)) {
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
        $headCommentTokens = array();
        if (self::$useLeadingNewLine) {
            $headCommentTokens[] = new Token(array(T_WHITESPACE, "\n"));
        }

        if ('' !== self::$headerComment) {
            $token = self::$useDocBlockComment ? T_DOC_COMMENT : T_COMMENT;
            $headCommentTokens[] = new Token(array($token, self::$headerComment));
            $headCommentTokens[] = new Token(array(T_WHITESPACE, "\n\n"));
        }

        $tokens->insertAt($index, $headCommentTokens);
    }
}
