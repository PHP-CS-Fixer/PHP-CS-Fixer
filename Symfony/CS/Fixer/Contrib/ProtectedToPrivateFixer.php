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
 */
final class ProtectedToPrivateFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $classes = array_keys($tokens->findGivenKind(T_CLASS));
        $classyElements = count($classes) ? $tokens->getClassyElements() : array();
        end($classyElements);

        while ($classIndex = array_pop($classes)) {
            // Must be done before skipClass() to fill correctly
            // the possible next $currentClassyElements
            $currentClassyElements = array();
            while (null !== ($index = key($classyElements)) && $index > $classIndex) {
                $currentClassyElements[$index] = current($classyElements);
                prev($classyElements);
            }

            if ($this->skipClass($tokens, $classIndex)) {
                continue;
            }

            foreach ($currentClassyElements as $index => $token) {
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

        return $tokens->generateCode();
    }

    /**
     * Decide whether or not skip the fix for given class.
     *
     * @param Tokens $tokens     the Tokens instance
     * @param int    $classIndex the class start index
     *
     * @return bool
     */
    private function skipClass(Tokens $tokens, $classIndex)
    {
        $prevTokenIndex = $tokens->getPrevMeaningfulToken($classIndex);
        $prevToken = $tokens[$prevTokenIndex];

        if ($prevToken->isGivenKind(T_ABSTRACT) || !$prevToken->isGivenKind(T_FINAL)) {
            return true;
        }

        $classOpeningIndex = $tokens->getNextTokenOfKind($classIndex, array('{'));
        $classCloseIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpeningIndex);

        $useIndex = $tokens->getNextTokenOfKind($classIndex, array(array(T_USE)));
        if ($useIndex && $useIndex < $classCloseIndex) {
            return true;
        }

        $extendsIndex = $classOpeningIndex;
        while ($extendsIndex > $classIndex) {
            --$extendsIndex;

            if ($tokens[$extendsIndex]->isGivenKind(T_EXTENDS)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Converts all protected variables and methods to private if needed.';
    }
}
