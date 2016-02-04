<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
            $startAndEnd = $this->findShortDescriptionStartAndEnd($doc->getLines());

            if (null === $startAndEnd) {
                continue;
            }

            $lastLine = $doc->getLine($startAndEnd[1]);
            $content = rtrim($lastLine->getContent());

            if (!$this->isEndOfShortDescriptionValid($content)) {
                $lastLine->setContent($content.".\n");
                $token->setContent($doc->getContent());
            }

            $firstLine = $doc->getLine($startAndEnd[0]);
            $content = $firstLine->getContent();

            $firstAsterisk = strpos($content, '*');
            // find first non white space char
            for ($i = $firstAsterisk + 1, $length = strlen($content); $i < $length; ++$i) {
                if (' ' !== $content[$i] && "\t" !== $content[$i]) {
                    break;
                }
            }

            // * [nw]
            if (2 === $i - $firstAsterisk) {
                if (' ' !== $content[$firstAsterisk + 1]) {
                    $content[$firstAsterisk + 1] = ' ';
                    $firstLine->setContent($content);
                    $token->setContent($doc->getContent());
                }

                continue;
            }

            // *[nw]
            if (1 === $i - $firstAsterisk) {
                $firstLine->setContent(substr($content, 0, $firstAsterisk + 1).' '.substr($content, $firstAsterisk + 1));
                $token->setContent($doc->getContent());
                continue;
            }

            // *(2+)[nw]
            $firstLine->setContent(substr($content, 0, $firstAsterisk + 1).' '.substr($content, $i));
            $token->setContent($doc->getContent());
            continue;
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
     * @return int[]|null Start and end index
     */
    private function findShortDescriptionStartAndEnd(array $lines)
    {
        $reachedContent = false;

        foreach ($lines as $index => $line) {

            // we went past a description, then hit a tag or blank line, so
            // the last line of the description must be the one before this one
            if (false !== $reachedContent && ($line->containsATag() || !$line->containsUsefulContent())) {
                return array($reachedContent, $index - 1);
            }

            // no short description was found
            if ($line->containsATag()) {
                return;
            }

            // we've reached content, but need to check the next lines too
            // in case the short description is multi-line
            if (false === $reachedContent && $line->containsUsefulContent()) {
                $reachedContent = $index;
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
    private function isEndOfShortDescriptionValid($content)
    {
        if (false !== strpos(strtolower($content), '{@inheritdoc}')) {
            return true;
        }

        return $content !== rtrim($content, '.。!?¡¿！？');
    }
}
