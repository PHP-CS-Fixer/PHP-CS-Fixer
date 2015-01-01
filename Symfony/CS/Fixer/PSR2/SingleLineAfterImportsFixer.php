<?php

/*
 * This file is part of the Symfony CS utility.
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
use Symfony\CS\Tokenizer\TokensAnalyzer;
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
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokensAnalyzer->getImportUseIndexes() as $index) {
            // if previous line ends with comment and current line starts with whitespace, use current indent
            if ($tokens[$index - 1]->isWhitespace(array('whitespaces' => " \t")) && $tokens[$index - 2]->isGivenKind(T_COMMENT)) {
                $indent = $tokens[$index - 1]->getContent();
            } elseif ($tokens[$index - 1]->isWhitespace()) {
                $indent = Utils::calculateTrailingWhitespaceIndent($tokens[$index - 1]);
            } else {
                $indent = '';
            }

            $newline = "\n";

            // Handle insert index for inline T_COMMENT with whitespace after semicolon
            $semicolonIndex = $tokens->getNextTokenOfKind($index, array(';', '{'));
            $insertIndex = $semicolonIndex + 1;
            if ($tokens[$insertIndex]->isWhitespace(array('whitespaces' => " \t")) && $tokens[$insertIndex + 1]->isComment()) {
                ++$insertIndex;
            }

            // Increment insert index for inline T_COMMENT or T_DOC_COMMENT
            if ($tokens[$insertIndex]->isComment()) {
                ++$insertIndex;
            }

            $afterSemicolon = $tokens->getNextMeaningfulToken($semicolonIndex);
            if (!$tokens[$afterSemicolon]->isGivenKind(T_USE)) {
                $newline .= "\n";
            }

            if ($tokens[$insertIndex]->isWhitespace()) {
                $nextToken = $tokens[$insertIndex];
                $nextToken->setContent($newline.$indent.ltrim($nextToken->getContent()));
            } elseif ($newline && $indent) {
                $tokens->insertAt($insertIndex, new Token(array(T_WHITESPACE, $newline.$indent)));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.';
    }
}
