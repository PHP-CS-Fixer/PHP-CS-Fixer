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
class LineAfterUseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $uses = $tokens->getImportUseIndexes();

        foreach ($uses as $index) {
            $semicolonIndex = $tokens->getNextTokenOfKind($index, array(';', '{'));

            $afterSemicolon = $tokens->getNextMeaningfulToken($semicolonIndex);
            $whitespace = "\n\n";
            if ($tokens[$afterSemicolon]->isGivenKind(T_USE)) {
                $whitespace = "\n";
            }
            $whitespace .= $this->calculateIndent($tokens[$index - 1]->getContent());

            $nextToken = $tokens[$semicolonIndex + 1];
            if (!$nextToken->isWhitespace()) {
                $tokens->insertAt($semicolonIndex + 1, new Token(array(T_WHITESPACE, $whitespace)));
                continue;
            }

            $nextToken->setContent($whitespace.ltrim($nextToken->getContent()));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Each namespace use MUST go on it\'s own line and there MUST be one blank line after the use statements block.';
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
