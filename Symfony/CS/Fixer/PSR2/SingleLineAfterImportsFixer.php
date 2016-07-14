<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Utils;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Ceeram <ceeram@cakephp.org>
 * @author Graham Campbell <graham@mineuk.com>
 */
class SingleLineAfterImportsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->getImportUseIndexes() as $index) {
            $indent = '';

            // if previous line ends with comment and current line starts with whitespace, use current indent
            if ($tokens[$index - 1]->isWhitespace(array('whitespaces' => " \t")) && $tokens[$index - 2]->isGivenKind(T_COMMENT)) {
                $indent = $tokens[$index - 1]->getContent();
            } elseif ($tokens[$index - 1]->isWhitespace()) {
                $indent = Utils::calculateTrailingWhitespaceIndent($tokens[$index - 1]);
            }

            $semicolonIndex = $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG))); // Handle insert index for inline T_COMMENT with whitespace after semicolon
            $insertIndex = $semicolonIndex;

            if ($tokens[$semicolonIndex]->isGivenKind(T_CLOSE_TAG)) {
                if ($tokens[$insertIndex - 1]->isWhitespace()) {
                    --$insertIndex;
                }

                $tokens->insertAt($insertIndex, new Token(';'));
            }

            if ($semicolonIndex === count($tokens) - 1) {
                $tokens->insertAt($insertIndex + 1, new Token(array(T_WHITESPACE, "\n\n".$indent)));
            } else {
                $newline = "\n";
                $tokens[$semicolonIndex]->isGivenKind(T_CLOSE_TAG) ? --$insertIndex : ++$insertIndex;
                if ($tokens[$insertIndex]->isWhitespace(array('whitespaces' => " \t")) && $tokens[$insertIndex + 1]->isComment()) {
                    ++$insertIndex;
                }

                // Do not add newline after inline T_COMMENT as it is part of T_COMMENT already (note: not needed on 2.x line )
                if ($tokens[$insertIndex]->isGivenKind(T_COMMENT) && false !== strpos($tokens[$insertIndex]->getContent(), "\n")) {
                    $newline = '';
                }

                // Increment insert index for inline T_COMMENT or T_DOC_COMMENT
                if ($tokens[$insertIndex]->isComment()) {
                    ++$insertIndex;
                }

                $afterSemicolon = $tokens->getNextMeaningfulToken($semicolonIndex);
                if (null === $afterSemicolon || !$tokens[$afterSemicolon]->isGivenKind(T_USE)) {
                    $newline .= "\n";
                }

                if ($tokens[$insertIndex]->isWhitespace()) {
                    $nextToken = $tokens[$insertIndex];
                    $nextMeaningfulAfterUseIndex = $tokens->getNextMeaningfulToken($insertIndex);
                    if (null !== $nextMeaningfulAfterUseIndex && $tokens[$nextMeaningfulAfterUseIndex]->isGivenKind(T_USE)) {
                        if (substr_count($nextToken->getContent(), "\n") < 2) {
                            $nextToken->setContent($newline.$indent.ltrim($nextToken->getContent()));
                        }
                    } else {
                        $nextToken->setContent($newline.$indent.ltrim($nextToken->getContent()));
                    }
                } else {
                    if ('' !== $newline.$indent) { // (note: check not needed on 2.x line)
                        $tokens->insertAt($insertIndex, new Token(array(T_WHITESPACE, $newline.$indent)));
                    }
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
        return 'Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.';
    }
}
