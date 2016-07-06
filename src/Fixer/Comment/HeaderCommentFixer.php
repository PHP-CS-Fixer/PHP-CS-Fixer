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
 */
final class HeaderCommentFixer extends AbstractFixer
{
    private $header = '';
    private $headerComment = '';

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration || !isset($configuration['header'])) {
            throw new InvalidFixerConfigurationException($this->getName(), 'Configuration is required.');
        }

        $header = $configuration['header'];

        if (!is_string($header)) {
            throw new InvalidFixerConfigurationException($this->getName(), sprintf('Header configuration is invalid. Expected "string", got "%s".', is_object($header) ? get_class($header) : gettype($header)));
        }

        $this->setHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        if (!$tokens[0]->isGivenKind(T_OPEN_TAG) || !$tokens->isMonolithicPhp()) {
            return;
        }

        $oldHeaderIndex = $this->findHeaderCommentIndex($tokens);
        $newHeaderIndex = $this->findHeaderCommentInsertionIndex($tokens);

        if (
            $oldHeaderIndex === $newHeaderIndex
            && $this->headerComment === $tokens[$oldHeaderIndex]->getContent()
        ) {
            return;
        }

        $this->replaceHeaderComment($tokens, $oldHeaderIndex, $newHeaderIndex);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Add, replace or remove header comment.';
    }

    /**
     * Enclose the given text in a comment block.
     *
     * @param string $header
     *
     * @return string
     */
    private function encloseTextInComment($header)
    {
        $comment = "/*\n";
        $lines = explode("\n", str_replace("\r", '', $header));
        foreach ($lines as $line) {
            $comment .= rtrim(' * '.$line)."\n";
        }
        $comment .= ' */';

        return $comment;
    }

    /**
     * Find the closest whitespace block before the header comment index.
     *
     * @param Tokens $tokens
     *
     * @return int|null
     */
    private function findHeaderCommentIndex(Tokens $tokens)
    {
        $index = $tokens->getNextNonWhitespace(0);
        if (null !== $index && $tokens[$index]->isGivenKind(T_DECLARE)) {
            $index = $this->skipDeclare($tokens, $index);
        }

        if (null !== $index && $tokens[$index]->isGivenKind(T_COMMENT)) {
            return $index === 2 && $tokens[1]->isWhitespace() ? 1 : $index;
        }
    }

    /**
     * Find the closest whitespace block where the header comment must be inserted.
     *
     * @param Tokens $tokens
     *
     * @return int
     */
    private function findHeaderCommentInsertionIndex(Tokens $tokens)
    {
        $index = $tokens->getNextNonWhitespace(0);
        if (null !== $index && $tokens[$index]->isGivenKind(T_DECLARE)) {
            $index = $this->skipDeclare($tokens, $index);
        }

        if (null === $index) {
            // empty file, insert at the end
            $index = $tokens->getSize();
        }

        return $index === 2 && $tokens[1]->isWhitespace() ? 1 : $index;
    }

    /**
     * Skips a declare(strict_type=1); statement as it is allowed to be before the header.
     *
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function skipDeclare(Tokens $tokens, $index)
    {
        $isOpen = false;
        $isStrictTypes = false;
        $originalIndex = $index;

        while ($index = $tokens->getNextNonWhitespace($index)) {
            if ($tokens[$index]->getContent() === '(') {
                $isOpen = true;
            }
            if ($isOpen && $tokens[$index]->getContent() === 'strict_types') {
                $isStrictTypes = true;
            }
            if ($tokens[$index]->getContent() === ';') {
                ++$index;
                break;
            }
        }

        return $isStrictTypes ? $index : $originalIndex;
    }

    /**
     * Replace the header comment at the given index.
     *
     * @param Tokens $tokens
     * @param int    $oldHeaderIndex
     * @param int    $headerInsertionIndex
     */
    private function replaceHeaderComment(Tokens $tokens, $oldHeaderIndex, $headerInsertionIndex)
    {
        $headerEnd = null !== $oldHeaderIndex
            ? $tokens->getNextNonWhitespace($oldHeaderIndex)
            : $headerInsertionIndex - 1
        ;

        while (isset($tokens[$headerEnd + 1]) && ($tokens[$headerEnd + 1]->isWhitespace() || $tokens[$headerEnd + 1]->isGivenKind(T_COMMENT))) {
            ++$headerEnd;
        }

        if ('' === $this->headerComment) {
            if ($oldHeaderIndex) {
                $tokens->clearRange($tokens->getNextNonWhitespace($oldHeaderIndex), $headerEnd);
            }

            return;
        }

        $headCommentTokens = array(
            new Token(array(T_WHITESPACE, $headerInsertionIndex === 1 ? "\n" : "\n\n")),
            new Token(array(T_COMMENT, $this->headerComment)),
            new Token(array(T_WHITESPACE, "\n\n")),
        );

        $tokens->overrideRange($headerInsertionIndex, $headerEnd, $headCommentTokens);
    }

    /**
     * Set the desired header text.
     *
     * The given text will be trimmed and enclosed into a multiline comment.
     * If the text is empty, when a file get fixed, the header comment will be
     * erased.
     *
     * @param string $header
     */
    private function setHeader($header)
    {
        $this->header = trim($header);
        $this->headerComment = '';

        if ('' !== $this->header) {
            $this->headerComment = $this->encloseTextInComment($this->header);
        }
    }
}
