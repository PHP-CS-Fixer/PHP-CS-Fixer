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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class PhpdocTrimFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Phpdocs should start and end with content, excluding the very first and last line of the docblocks.',
            array(new CodeSample('<?php
/**
 *
 * Foo must be final class.
 *
 *
 */
final class Foo {}
'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        /*
         * Should be run after all phpdoc fixers that add or remove tags, or
         * alter descriptions. This is so that they don't leave behind blank
         * lines this fixer would have otherwise cleaned up.
         */
        return -5;
    }

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $content = $token->getContent();
            $content = $this->fixStart($content);
            // we need re-parse the docblock after fixing the start before
            // fixing the end in order for the lines to be correctly indexed
            $content = $this->fixEnd($content);
            $tokens[$index] = new Token(array(T_DOC_COMMENT, $content));
        }
    }

    /**
     * Make sure the first useful line starts immediately after the first line.
     *
     * @param string $content
     *
     * @return string
     */
    private function fixStart($content)
    {
        $doc = new DocBlock($content);
        $lines = $doc->getLines();
        $total = count($lines);

        foreach ($lines as $index => $line) {
            if (!$line->isTheStart()) {
                // don't remove lines with content and don't entirely delete docblocks
                if ($total - $index < 3 || $line->containsUsefulContent()) {
                    break;
                }

                $line->remove();
            }
        }

        return $doc->getContent();
    }

    /**
     * Make sure the last useful is immediately before after the final line.
     *
     * @param string $content
     *
     * @return string
     */
    private function fixEnd($content)
    {
        $doc = new DocBlock($content);
        $lines = array_reverse($doc->getLines());
        $total = count($lines);

        foreach ($lines as $index => $line) {
            if (!$line->isTheEnd()) {
                // don't remove lines with content and don't entirely delete docblocks
                if ($total - $index < 3 || $line->containsUsefulContent()) {
                    break;
                }

                $line->remove();
            }
        }

        return $doc->getContent();
    }
}
