<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
class ProtectedToPrivateFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->skipFix($tokens)) {
            return $content;
        }

        $elements = $tokens->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $token) {
            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];

            if ($prevToken->isGivenKind(T_STATIC)) {
                $prevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                $prevToken = $tokens[$prevTokenIndex];
            }

            if (!$prevToken->isGivenKind(T_PROTECTED)) {
                continue;
            }

            $prevToken->clear();
            $tokens->insertAt(
                $prevTokenIndex,
                new Token(array(T_PRIVATE, 'private'))
            );
        }

        return $tokens->generateCode();
    }

    /**
     * Decide whether or not skip the fix.
     *
     * @param Tokens $tokens the Tokens instance
     *
     * @return bool
     */
    private function skipFix(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CLASS)) {
                continue;
            }

            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];

            if ($prevToken->isGivenKind(T_ABSTRACT)) {
                return true;
            }

            $useIndex = $tokens->getNextTokenOfKind($index, array(array(T_USE)));
            if ($useIndex > 0) {
                return true;
            }

            $classOpeningIndex = $tokens->getNextTokenOfKind($index, array('{'));
            $extendsIndex = $classOpeningIndex;

            $extendsPresent = false;
            while ($extendsIndex > $index) {
                --$extendsIndex;

                if ($tokens[$extendsIndex]->isGivenKind(T_EXTENDS)) {
                    $extendsPresent = true;
                }
            }

            if ($extendsPresent or !$prevToken->isGivenKind(T_FINAL)) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Converts all protected variables and methods to private if needed.';
    }
}
