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

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 * @author SpacePossum
 */
final class HeaderCommentFixer extends AbstractFixer
{
    const HEADER_PHPDOC = 'PHPDoc';
    const HEADER_COMMENT = 'comment';

    const HEADER_LOCATION_AFTER_OPEN = 1;
    const HEADER_LOCATION_AFTER_DECLARE_STRICT = 2;

    const HEADER_LINE_SEPARATION_BOTH = 1;
    const HEADER_LINE_SEPARATION_TOP = 2;
    const HEADER_LINE_SEPARATION_BOTTOM = 3;
    const HEADER_LINE_SEPARATION_NONE = 4;

    /** @var string */
    private $headerComment;

    /** @var int */
    private $headerCommentType;

    /** @var int */
    private $headerLocation;

    /** @var int */
    private $headerLineSeparation;

    /**
     * {@inheritdoc}
     *
     * The following configuration options are allowed:
     * - commentType  PHPDoc|comment*
     * - location     after_open|after_declare_strict*
     * - separate     top|bottom|none|both*
     *
     * (* is the default when the item is omitted)
     */
    public function configure(array $configuration = null)
    {
        list(
            $this->headerComment,
            $this->headerCommentType,
            $this->headerLocation,
            $this->headerLineSeparation) = $this->parseConfiguration($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // figure out where the comment should be placed
        $headerNewIndex = $this->findHeaderCommentInsertionIndex($tokens);

        // check if there is already a comment
        $headerCurrentIndex = $this->findHeaderCommentCurrentIndex($tokens, $headerNewIndex - 1);

        if (null === $headerCurrentIndex) {
            if ('' === $this->headerComment) {
                return; // header not found and none should be set, return
            }

            $this->insertHeader($tokens, $headerNewIndex);
        } elseif ($this->headerComment !== $tokens[$headerCurrentIndex]->getContent()) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($headerCurrentIndex);
            if ('' === $this->headerComment) {
                return; // header found and cleared, none should be set, return
            }

            $this->insertHeader($tokens, $headerNewIndex);
        } else {
            $headerNewIndex = $headerCurrentIndex;
        }

        $this->fixWhiteSpaceAroundHeader($tokens, $headerNewIndex);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Add, replace or remove header comment.';
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens[0]->isGivenKind(T_OPEN_TAG) && $tokens->isMonolithicPhp();
    }

    /**
     * Enclose the given text in a comment block.
     *
     * @param string $header
     * @param int    $type
     *
     * @return string
     */
    private function encloseTextInComment($header, $type)
    {
        $comment = self::HEADER_COMMENT === $type ? "/*\n" : "/**\n";
        $lines = explode("\n", str_replace("\r", '', $header));
        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line)."\n";
        }

        return $comment.' */';
    }

    /**
     * Find the header comment index.
     *
     * @param Tokens $tokens
     * @param int    $headerNewIndex
     *
     * @return int|null
     */
    private function findHeaderCommentCurrentIndex(Tokens $tokens, $headerNewIndex)
    {
        $index = $tokens->getNextNonWhitespace($headerNewIndex);

        return null === $index || !$tokens[$index]->isComment() ? null : $index;
    }

    /**
     * Find the index where the header comment must be inserted.
     *
     * @param Tokens $tokens
     *
     * @return int
     */
    private function findHeaderCommentInsertionIndex(Tokens $tokens)
    {
        if (self::HEADER_LOCATION_AFTER_OPEN === $this->headerLocation) {
            return 1;
        }

        $index = $tokens->getNextMeaningfulToken(0);
        if (null === $index) {
            // file without meaningful tokens but an open tag, comment should always be placed directly after the open tag
            return 1;
        }

        if (!$tokens[$index]->isGivenKind(T_DECLARE)) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($index);
        if (null === $next || !$tokens[$next]->equals('(')) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals(array(T_STRING, 'strict_types'), false)) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals('=')) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->isGivenKind(T_LNUMBER)) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals(')')) {
            return 1;
        }

        $next = $tokens->getNextMeaningfulToken($next);
        if (null === $next || !$tokens[$next]->equals(';')) { // don't insert after close tag
            return 1;
        }

        return $next + 1;
    }

    /**
     * @param Tokens $tokens
     * @param int    $headerIndex
     */
    private function fixWhiteSpaceAroundHeader(Tokens $tokens, $headerIndex)
    {
        // fix lines after header comment
        $expectedLineCount = self::HEADER_LINE_SEPARATION_BOTH === $this->headerLineSeparation || self::HEADER_LINE_SEPARATION_BOTTOM === $this->headerLineSeparation ? 2 : 1;
        if ($headerIndex === count($tokens) - 1) {
            $tokens->insertAt($headerIndex + 1, new Token(array(T_WHITESPACE, str_repeat("\n", $expectedLineCount))));
        } else {
            $afterCommentIndex = $tokens->getNextNonWhitespace($headerIndex);
            $lineBreakCount = $this->getLineBreakCount($tokens, $headerIndex + 1, null === $afterCommentIndex ? count($tokens) : $afterCommentIndex);
            if ($lineBreakCount < $expectedLineCount) {
                $missing = str_repeat("\n", $expectedLineCount - $lineBreakCount);
                if ($tokens[$headerIndex + 1]->isWhitespace()) {
                    $tokens[$headerIndex + 1]->setContent($missing.$tokens[$headerIndex + 1]->getContent());
                } else {
                    $tokens->insertAt($headerIndex + 1, new Token(array(T_WHITESPACE, $missing)));
                }
            }
        }

        // fix lines before header comment
        $expectedLineCount = self::HEADER_LINE_SEPARATION_BOTH === $this->headerLineSeparation || self::HEADER_LINE_SEPARATION_TOP === $this->headerLineSeparation ? 2 : 1;
        $lineBreakCount = $this->getLineBreakCount($tokens, $tokens->getPrevNonWhitespace($headerIndex), $headerIndex);
        if ($lineBreakCount < $expectedLineCount) {
            // because of the way the insert index was determined for header comment there cannot be an empty token here
            $tokens->insertAt($headerIndex, new Token(array(T_WHITESPACE, str_repeat("\n", $expectedLineCount - $lineBreakCount))));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $indexStart
     * @param int    $indexEnd
     *
     * @return int
     */
    private function getLineBreakCount(Tokens $tokens, $indexStart, $indexEnd)
    {
        $lineCount = 0;
        for ($i = $indexStart; $i < $indexEnd; ++$i) {
            $lineCount += substr_count($tokens[$i]->getContent(), "\n");
        }

        return $lineCount;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function insertHeader(Tokens $tokens, $index)
    {
        $tokens->insertAt($index, new Token(array(self::HEADER_COMMENT === $this->headerCommentType ? T_COMMENT : T_DOC_COMMENT, $this->headerComment)));
    }

    /**
     * @param array|null $configuration
     *
     * @return array
     */
    private function parseConfiguration(array $configuration = null)
    {
        if (null === $configuration || !array_key_exists('header', $configuration)) {
            throw new InvalidFixerConfigurationException($this->getName(), 'Configuration is required.');
        }

        $header = $configuration['header'];
        if (!is_string($header)) {
            throw new InvalidFixerConfigurationException($this->getName(), sprintf('Header configuration is invalid. Expected "string", got "%s".', is_object($header) ? get_class($header) : gettype($header)));
        }

        if (array_key_exists('commentType', $configuration)) {
            $commentType = $configuration['commentType'];
            if (!in_array($commentType, array(self::HEADER_PHPDOC, self::HEADER_COMMENT), true)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('Header type configuration is invalid, expected "PHPDoc" or "comment", got "%s".', is_object($commentType) ? get_class($commentType) : var_export($commentType, true)));
            }
        } else {
            $commentType = self::HEADER_COMMENT;
        }

        $header = '' === trim($header) ? '' : $this->encloseTextInComment($header, $commentType);

        if (array_key_exists('location', $configuration)) {
            $location = $configuration['location'];
            switch ($location) {
                case 'after_open':
                    $location = self::HEADER_LOCATION_AFTER_OPEN;
                    break;
                case 'after_declare_strict':
                    $location = self::HEADER_LOCATION_AFTER_DECLARE_STRICT;
                    break;
                default:
                    throw new InvalidFixerConfigurationException($this->getName(), sprintf('Header location configuration is invalid, expected "after_open" or "after_declare_strict", got "%s".', is_object($location) ? get_class($location) : var_export($location, true)));
            }
        } else {
            $location = self::HEADER_LOCATION_AFTER_DECLARE_STRICT;
        }

        if (array_key_exists('separate', $configuration)) {
            $headerLineSeparation = $configuration['separate'];
            switch ($headerLineSeparation) {
                case 'both':
                    $headerLineSeparation = self::HEADER_LINE_SEPARATION_BOTH;
                    break;
                case 'top':
                    $headerLineSeparation = self::HEADER_LINE_SEPARATION_TOP;
                    break;
                case 'bottom':
                    $headerLineSeparation = self::HEADER_LINE_SEPARATION_BOTTOM;
                    break;
                case 'none':
                    $headerLineSeparation = self::HEADER_LINE_SEPARATION_NONE;
                    break;
                default:
                    throw new InvalidFixerConfigurationException($this->getName(), sprintf('Header separate configuration is invalid, expected "both", "top", "bottom" or "none", got "%s".', is_object($headerLineSeparation) ? get_class($headerLineSeparation) : var_export($headerLineSeparation, true)));
            }
        } else {
            $headerLineSeparation = self::HEADER_LINE_SEPARATION_BOTH;
        }

        return array(
            $header,
            $commentType,
            $location,
            $headerLineSeparation,
        );
    }
}
