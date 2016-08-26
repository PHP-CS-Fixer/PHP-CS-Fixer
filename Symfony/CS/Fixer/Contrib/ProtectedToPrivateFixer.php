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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author SpacePossum
 */
final class ProtectedToPrivateFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $end = count($tokens) - 3; // min. number of tokens to form a class candidate to fix
        for ($index = 0; $index < $end; ++$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS)) {
                continue;
            }

            $classOpen = $tokens->getNextTokenOfKind($index, array('{'));
            $classClose = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpen);

            if (!$this->skipClass($tokens, $index, $classOpen, $classClose)) {
                $this->fixClass($tokens, $index, $classOpen, $classClose);
            }

            $index = $classClose;
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Converts protected variables and methods to private where possible.';
    }

    /**
     * @param Tokens $tokens
     * @param int    $classIndex
     * @param int    $classOpenIndex
     * @param int    $classCloseIndex
     */
    private function fixClass(Tokens $tokens, $classIndex, $classOpenIndex, $classCloseIndex)
    {
        for ($index = $classOpenIndex + 1; $index < $classCloseIndex; ++$index) {
            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];

            if ($prevToken->isGivenKind(T_STATIC)) {
                $prevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                $prevToken = $tokens[$prevTokenIndex];
            }

            if (!$prevToken->isGivenKind(T_PROTECTED)) {
                continue;
            }

            $tokens->overrideAt($prevTokenIndex, array(T_PRIVATE, 'private'));
        }
    }

    /**
     * Decide whether or not skip the fix for given class.
     *
     * @param Tokens $tokens
     * @param int    $classIndex
     * @param int    $classOpenIndex
     * @param int    $classCloseIndex
     *
     * @return bool
     */
    private function skipClass(Tokens $tokens, $classIndex, $classOpenIndex, $classCloseIndex)
    {
        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($classIndex)];
        if ($prevToken->isGivenKind(T_ABSTRACT) || !$prevToken->isGivenKind(T_FINAL)) {
            return true;
        }

        for ($i = $classIndex; $i < $classOpenIndex; ++$i) {
            if ($tokens[$i]->isGivenKind(T_EXTENDS)) {
                return true;
            }
        }

        $useIndex = $tokens->getNextTokenOfKind($classIndex, array(array(T_USE)));

        return $useIndex && $useIndex < $classCloseIndex;
    }
}
