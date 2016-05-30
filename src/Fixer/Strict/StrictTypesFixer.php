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

namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author SpacePossum
 */
final class StrictTypesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return count($tokens) > 1 && $tokens[0]->isGivenKind(T_OPEN_TAG);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // check for if declaration already in file
        $sequence = $this->getDeclareStrictTypeSequence();
        $searchIndex = $tokens->getNextMeaningfulToken(0);
        if (null === $searchIndex) {
            $this->insertNewSequence($tokens); // not found, insert declare statement

            return;
        }

        $sequenceLocation = $tokens->findSequence($sequence, $searchIndex, null, false);
        if (null === $sequenceLocation) {
            $this->insertNewSequence($tokens); // not found, insert declare statement

            return;
        }

        // found, fix casing
        $this->fixSequenceCasing($sequenceLocation);

        end($sequenceLocation);
        $sequenceEndIndex = $tokens->getNextMeaningfulToken(key($sequenceLocation));
        if ($tokens[$sequenceEndIndex]->isGivenKind(T_CLOSE_TAG)) {
            return; // don't move, comment placement might cause invalid code if moved
        }

        // check if the declaration is at the right location
        reset($sequenceLocation);
        $sequenceStartIndex = key($sequenceLocation);
        if (1 === $sequenceStartIndex) {
            // already at the correct location
            $this->fixSpaceAroundSequence($tokens, $sequenceStartIndex, $sequenceEndIndex);

            return;
        }

        $sequenceLocation[$sequenceEndIndex] = $tokens[$sequenceEndIndex];
        $this->moveSequence($tokens, $sequenceLocation);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Force strict types declaration in all files.';
    }

    /**
     * @param Token[] $sequenceLocation
     */
    private function fixSequenceCasing(array $sequenceLocation)
    {
        foreach ($sequenceLocation as $token) {
            $token->setContent(strtolower($token->getContent()));
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function fixSpaceAroundSequence(Tokens $tokens, $startIndex, $endIndex)
    {
        if ($tokens[1 + $endIndex]->isWhitespace()) {
            $content = $tokens[1 + $endIndex]->getContent();
            if (false === strpos($content, "\n")) {
                $tokens[1 + $endIndex]->setContent("\n".ltrim($content));
            }
        } else {
            $tokens->insertAt(1 + $endIndex, new Token(array(T_WHITESPACE, "\n")));
        }

        // transform "<?php\n" to "<?php " if needed
        if (false !== strpos($tokens[0]->getContent(), "\n")) {
            $tokens[0]->setContent(trim($tokens[0]->getContent()).' ');
        } elseif ($tokens[1]->isWhitespace()) {
            $tokens[1]->setContent(' ');
        }
    }

    /**
     * @param Tokens $tokens
     */
    private function insertNewSequence(Tokens $tokens)
    {
        $sequence = $this->getDeclareStrictTypeSequence();
        $sequence[] = new Token(';');
        $tokens->insertAt(1, $sequence);
        $this->fixSpaceAroundSequence($tokens, 1, count($sequence));
    }

    /**
     * @param Tokens  $tokens
     * @param Token[] $sequenceLocation
     */
    private function moveSequence(Tokens $tokens, array $sequenceLocation)
    {
        $sequence = array();
        foreach ($sequenceLocation as $index => $token) {
            $sequence[] = clone $token;
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }

        $tokens->insertAt(1, $sequence);
        $this->fixSpaceAroundSequence($tokens, 1, count($sequence));
    }

    /**
     * @return Token[]
     */
    private function getDeclareStrictTypeSequence()
    {
        static $sequence = null;

        // do not look for open tag, closing semicolon or empty lines;
        // - open tag is tested by isCandidate
        // - semicolon or end tag must be there to be valid PHP
        // - empty lines are to be dealt with later
        if (null === $sequence) {
            $sequence = array(
                new Token(array(T_DECLARE, 'declare')),
                new Token('('),
                new Token(array(T_STRING, 'strict_types')),
                new Token('='),
                new Token(array(T_LNUMBER, '1')),
                new Token(')'),
            );
        }

        return $sequence;
    }
}
