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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Adds a blank line before phpdoc.
 *
 * Adds a blank line before phpdoc,
 * if preceded by a ";" or a "}" or a comment or a docblock, unless that docblock is an inline @var
 *
 * @author Jonathan Daigle
 */
final class SingleBlankLineBeforePhpdocFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            /** @var Token $token */
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            /* @var Token $previousToken */
            $previousIndex = $tokens->getPrevNonWhitespace($index);
            $previousToken = $tokens[$previousIndex];

            // If the previous token token is a ; or a } or a comment but not an @var inline comment
            // Then a blank line must passed
            $requiredDistance = 1;
            $content = $previousToken->getContent();
            if ($content === ';' || $content === '}' || ($previousToken->isComment() && !$this->isInlineAtVar($content))) {
                $requiredDistance = 2;
            }

            $distance = $this->getNbLinesInBetween($tokens, $previousIndex, $index);

            $needDistance = $requiredDistance - $distance;
            if ($needDistance > 0) {
                $index += $this->addNewLineAt($tokens, $index, $needDistance);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Adds a blank line before phpdoc, if preceded by a ";" or a "}" or a comment or a docblock, unless that docblock is an inline @var.';
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * Insert 1 line at $index.
     *
     * @return 0|1 Increment for the index
     */
    private function addNewLineAt(Tokens $tokens, $index, $nbLines = 1)
    {
        $prevToken = $tokens[$index - 1];

        if ($prevToken->isWhitespace()) {
            $parts = explode("\n", $prevToken->getContent());
            $countParts = count($parts);

            if (1 === $countParts) {
                $prevToken->setContent(rtrim($prevToken->getContent(), " \t").str_repeat("\n", $nbLines));
            } elseif (count($parts) <= 2) {
                $prevToken->setContent(str_repeat("\n", $nbLines).$prevToken->getContent());
            }

            return 0;
        }

        $tokens->insertAt($index, new Token(array(T_WHITESPACE, str_repeat("\n", $nbLines))));

        return 1;
    }

    /**
     * Get number of lines Between Tokens index.
     *
     * @return int
     */
    private function getNbLinesInBetween(Tokens $tokens, $start, $end)
    {
        $distance = 0;
        if (!$tokens[$start]->isComment()) {
            $distance += substr_count($tokens[$start]->getContent(), "\n");
        }

        for ($i = $start + 1; $i < $end; ++$i) {
            $token = $tokens[$i];
            $distance += substr_count($token->getContent(), "\n");
        }

        return $distance;
    }

    /**
     * @param string $content
     *
     * @return bool
     */
    private function isInlineAtVar($content)
    {
        return (bool) preg_match('|\/\*\* +@var +(\$?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\\\\]* ?){1,2} *\*\/|', $content);
    }
}
