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
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class HeaderCommentFixer extends AbstractFixer
{
    private static $header = '';
    private static $headerComment = '';

    /**
     * Sets the desired header text
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

        if (strlen(self::$header) !== 0) {
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

        if (!count($tokens) || $tokens[0]->getId() !== T_OPEN_TAG) {
            return $content;
        }

        $newContent  = $tokens[0]->getContent();
        $newContent .= PHP_EOL;
        $newContent .= strlen(self::$headerComment)>0 ? self::$headerComment.PHP_EOL : '';

        if (null !== $firstNonWhitespace = $tokens->getNextNonWhitespace(0)) {
            $indexStart = $firstNonWhitespace;
            if ($tokens[$firstNonWhitespace]->getId() === T_COMMENT) {
                $indexStart = $tokens->getNextNonWhitespace($firstNonWhitespace);
            }
        }

        if (null !== $indexStart) {
            $newContent .= $tokens->generatePartialCode($indexStart, $tokens->getSize()-1);
        }

        return $newContent;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Add or replace header comment.';
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        if ('php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION)) {
            return true;
        }

        return false;
    }

    /**
     * @param  string $header
     * @return string
     */
    private static function encloseTextInComment($header)
    {
        $comment = '/*'.PHP_EOL;
        $lines = explode("\n", str_replace("\r", '', $header));
        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line).PHP_EOL;
        }
        $comment .= ' */'.PHP_EOL;

        return $comment;
    }
}
