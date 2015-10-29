<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\DocBlock\Line;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocShortDescriptionFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
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

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Phpdocs short descriptions should end in either a full stop, exclamation mark, or question mark.';
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
