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

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Utils;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Ceeram <ceeram@cakephp.org>
 * @author Graham Campbell <graham@mineuk.com>
 */
final class SingleLineAfterImportsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_USE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        foreach ($tokensAnalyzer->getImportUseIndexes() as $index) {
            $indent = '';

            // if previous line ends with comment and current line starts with whitespace, use current indent
            if ($tokens[$index - 1]->isWhitespace(" \t") && $tokens[$index - 2]->isGivenKind(T_COMMENT)) {
                $indent = $tokens[$index - 1]->getContent();
            } elseif ($tokens[$index - 1]->isWhitespace()) {
                $indent = Utils::calculateTrailingWhitespaceIndent($tokens[$index - 1]);
            }

            $newline = "\n";

            // Handle insert index for inline T_COMMENT with whitespace after semicolon
            $semicolonIndex = $tokens->getNextTokenOfKind($index, array(';', '{'));
            $insertIndex = $semicolonIndex + 1;
            if ($tokens[$insertIndex]->isWhitespace(" \t") && $tokens[$insertIndex + 1]->isComment()) {
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
