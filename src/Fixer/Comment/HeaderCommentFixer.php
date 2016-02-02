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
            throw new InvalidFixerConfigurationException($this->getName(), 'Configuration is missing.');
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
        if (!$tokens->isMonolithicPhp()) {
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

        $this->replaceHeaderComment($tokens, $oldHeaderIndex);
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
     * Find the header comment index.
     *
     * @param Tokens $tokens
     *
     * @return int|null
     */
    private function findHeaderCommentIndex(Tokens $tokens)
    {
        $index = $tokens->getNextNonWhitespace(0);

        if (null !== $index && $tokens[$index]->isGivenKind(T_COMMENT)) {
            return $index;
        }
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
        $index = $tokens->getNextNonWhitespace(0);

        if (null === $index) {
            // empty file, insert at the end
            $index = $tokens->getSize();
        }

        return $index;
    }

    /**
     * Replace the header comment at the given index.
     *
     * @param Tokens $tokens
     * @param int    $oldHeaderIndex
     */
    private function replaceHeaderComment(Tokens $tokens, $oldHeaderIndex)
    {
        if ('' === $this->headerComment) {
            if ($oldHeaderIndex) {
                $tokens->clearRange($oldHeaderIndex, $oldHeaderIndex + 1);
            }

            return;
        }

        $headCommentTokens = array(
            new Token(array(T_WHITESPACE, "\n")),
            new Token(array(T_COMMENT, $this->headerComment)),
            new Token(array(T_WHITESPACE, "\n\n")),
        );

        $newHeaderIndex = null !== $oldHeaderIndex
            ? $oldHeaderIndex + 1
            : $this->findHeaderCommentInsertionIndex($tokens) - 1
        ;

        $tokens->overrideRange(1, $newHeaderIndex, $headCommentTokens);
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
