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

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
final class PhpdocSummaryFixer extends AbstractFixer
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
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($token->getContent());
            $end = $this->findShortDescriptionEnd($doc->getLines());

            if (null !== $end) {
                $line = $doc->getLine($end);
                $content = rtrim($line->getContent());

                if (!$this->isCorrectlyFormatted($content)) {
                    $line->setContent($content.".\n");
                    $token->setContent($doc->getContent());
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Phpdocs summary should end in either a full stop, exclamation mark, or question mark.';
    }

    /**
     * Find the line number of the line containing the end of the short
     * description, if present.
     *
     * @param Line[] $lines
     *
     * @return int|null
     */
    private function findShortDescriptionEnd(array $lines)
    {
        $reachedContent = false;

        foreach ($lines as $index => $line) {
            // we went past a description, then hit a tag or blank line, so
            // the last line of the description must be the one before this one
            if ($reachedContent && ($line->containsATag() || !$line->containsUsefulContent())) {
                return $index - 1;
            }

            // no short description was found
            if ($line->containsATag()) {
                return;
            }

            // we've reached content, but need to check the next lines too
            // in case the short description is multi-line
            if ($line->containsUsefulContent()) {
                $reachedContent = true;
            }
        }
    }

    /**
     * Is the last line of the short description correctly formatted?
     *
     * @param string $content
     *
     * @return bool
     */
    private function isCorrectlyFormatted($content)
    {
        if (false !== strpos(strtolower($content), '{@inheritdoc}')) {
            return true;
        }

        return $content !== rtrim($content, '.。!?¡¿！？');
    }
}
