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

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Ceeram <ceeram@cakephp.org>
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
            // if previous line ends with comment and current line starts with whitespace, use current indent
            if ($tokens[$index - 1]->isWhitespace(array('whitespaces' => " \t")) && $tokens[$index - 2]->isGivenKind(T_COMMENT)) {
                $indent = $tokens[$index - 1]->getContent();
            } else {
                $indent = $this->calculateIndent($tokens[$index - 1]->getContent());
            }

            $newline = "\n";

            // Handle insert index for inline T_COMMENT with whitespace after semicolon
            $semicolonIndex = $tokens->getNextTokenOfKind($index, array(';', '{'));
            $insertIndex = $semicolonIndex + 1;
            if ($tokens[$insertIndex]->isWhitespace(array('whitespaces' => " \t")) && $tokens[$insertIndex + 1]->isComment()) {
                ++$insertIndex;
            }

            // Do not add newline after inline T_COMMENT as it is part of T_COMMENT already
            if ($tokens[$insertIndex]->isGivenKind(T_COMMENT)) {
                $newline = "";
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
            } else {
                $tokens->insertAt($insertIndex, new Token(array(T_WHITESPACE, $newline.$indent)));
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

    /**
     * Calculate used indentation in whitespace.
     *
     * @param string $content Whitespace
     *
     * @return string
     */
    private function calculateIndent($content)
    {
        return ltrim(strrchr(str_replace(array("\r\n", "\r"), "\n", $content), 10), "\n");
    }
}
