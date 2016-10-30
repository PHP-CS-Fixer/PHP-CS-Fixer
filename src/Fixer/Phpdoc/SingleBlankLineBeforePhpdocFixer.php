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
 * Excepts for: first element in curly brackets, file level phpdoc and consecutive inline @var.
 * (Optional exception can be remove vie public member.)
 *
 * @author Jonathan Daigle
 */
final class SingleBlankLineBeforePhpdocFixer extends AbstractFixer
{
    /**
     * Exclude adding a blank line before inline `@var`, when the previous line is another inline `@var`.
     *
     * @var bool
     */
    public $excludeConsecutiveInlineAtVar = true;

    /**
     * Exclude adding a blank line before the file level doc-block.
     *
     * @var bool
     */
    public $excludeFileLevel = true;

    /**
     * Exclude adding a blank line before the first element inside curly brackets.
     *
     * @var bool
     */
    public $excludeFirstElementInBrackets = true;

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

            $previousIndex = $tokens->getPrevNonWhitespace($index);
            $nbLines = $this->getNbLinesInBetween($tokens, $previousIndex, $index);

            // If there is already a blank line, then all is good.
            if ($nbLines >= 2) {
                continue;
            }

            // If we are on the same line as previous token. Then insert line.
            if ($nbLines === 0) {
                $index += $this->addNewLineAt($tokens, $index);
            }

            // If we are at the beginning of file. Then it's a file doc-block, all is good
            if ($this->excludeFileLevel) {
                if (
                    $previousIndex === 0 || (
                        $previousIndex === 1 && $tokens[0]->isGivenKind(array(T_INLINE_HTML, T_WHITESPACE))
                    )
                ) {
                    continue;
                }
            }

            /** @var Token $previousToken */
            $previousToken = $tokens[$previousIndex];

            // No blank line after opening structure is ok.
            if ($this->excludeFirstElementInBrackets) {
                if ($previousToken->equals('{')) {
                    continue;
                }
            }

            // Multiple inline @var in a row are allowed.
            if ($this->excludeConsecutiveInlineAtVar) {
                if ($this->isInlineAtVar($token->getContent()) && $this->isInlineAtVar($previousToken->getContent())) {
                    continue;
                }
            }

            $index += $this->addNewLineAt($tokens, $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Adds a blank line before phpdoc, excepts for: first element in curly brackets "{ }", file level phpdoc and consecutive inline @var.';
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
    private function addNewLineAt(Tokens $tokens, $index)
    {
        $prevToken = $tokens[$index - 1];

        if ($prevToken->isWhitespace()) {
            $parts = explode("\n", $prevToken->getContent());
            $countParts = count($parts);

            if (1 === $countParts) {
                $prevToken->setContent(rtrim($prevToken->getContent(), " \t")."\n");
            } elseif (count($parts) <= 2) {
                $prevToken->setContent("\n".$prevToken->getContent());
            }

            return 0;
        }

        $tokens->insertAt($index, new Token(array(T_WHITESPACE, "\n")));

        return 1;
    }

    /**
     * Get number of lines Between Tokes index.
     *
     * @param int $index
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
