<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Ceeram <ceeram@cakephp.org>
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocIndentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($index);

            // skip if there is no next token or if next token is block end `}`
            if (null === $nextIndex || $tokens[$nextIndex]->equals('}')) {
                continue;
            }

            $prevToken = $tokens[$index - 1];

            // ignore inline docblocks
            if (
                $prevToken->isGivenKind(T_OPEN_TAG)
                || ($prevToken->isWhitespace(" \t") && !$tokens[$index - 2]->isGivenKind(T_OPEN_TAG))
                || $prevToken->equalsAny(array(';', '{'))
            ) {
                continue;
            }

            $indent = '';
            if ($tokens[$nextIndex - 1]->isWhitespace()) {
                $indent = Utils::calculateTrailingWhitespaceIndent($tokens[$nextIndex - 1]);
            }

            $prevToken->setContent($this->fixWhitespaceBefore($prevToken->getContent(), $indent));
            $token->setContent($this->fixDocBlock($token->getContent(), $indent));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Docblocks should have the same indentation as the documented subject.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        /*
         * Should be run before all other docblock fixers apart from the
         * phpdoc_to_comment fixer to make sure all fixers apply correct
         * indentation to new code they add, and the phpdoc_params fixer only
         * works on correctly indented docblocks. We also need to be running
         * after the psr2 indentation fixer for obvious reasons.
         * comments.
         */
        return 20;
    }

    /**
     * Fix indentation of Docblock.
     *
     * @param string $content Docblock contents
     * @param string $indent  Indentation to apply
     *
     * @return string Dockblock contents including correct indentation
     */
    private function fixDocBlock($content, $indent)
    {
        return ltrim(preg_replace('/^[ \t]*/m', $indent.' ', $content));
    }

    /**
     * Fix whitespace before the Docblock.
     *
     * @param string $content Whitespace before Docblock
     * @param string $indent  Indentation of the documented subject
     *
     * @return string Whitespace including correct indentation for Dockblock after this whitespace
     */
    private function fixWhitespaceBefore($content, $indent)
    {
        return rtrim($content, " \t").$indent;
    }
}
