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

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
class EmptyReturnFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_RETURN) as $index => $token) {
            if ($this->needFixing($tokens, $index)) {
                $this->clear($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A return statement wishing to return nothing should be simply "return".';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before NoUselessReturnFixer
        return -17;
    }

    /**
     * Does the return statement located at a given index need fixing?
     *
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function needFixing(Tokens $tokens, $index)
    {
        $content = '';

        while (!$tokens[$index]->equals(';')) {
            $index = $tokens->getNextMeaningfulToken($index);
            $content .= $tokens[$index]->getContent();
        }

        $content = rtrim($content, ';');
        $content = ltrim($content, '(');
        $content = rtrim($content, ')');

        return 'null' === strtolower($content);
    }

    /**
     * Clear the return statement located at a given index.
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    private function clear(Tokens $tokens, $index)
    {
        while (!$tokens[++$index]->equals(';')) {
            if ($this->shouldClearToken($tokens, $index)) {
                $tokens[$index]->clear();
            }
        }
    }

    /**
     * Should we clear the specific token?
     *
     * If the token is a comment, or is whitespace that is immediately before a
     * comment, then we'll leave it alone.
     *
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function shouldClearToken(Tokens $tokens, $index)
    {
        $token = $tokens[$index];

        if ($token->isComment()) {
            return false;
        }

        if ($token->isWhitespace() && $tokens[$index + 1]->isComment()) {
            return false;
        }

        return true;
    }
}
