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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class OperatorsSpacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens->isBinaryOperator($index)) {
                continue;
            }

            // skip `declare(foo ==bar)`
            $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_STRING)) {
                $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
                if ($tokens[$prevMeaningfulIndex]->equals('(')) {
                    $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($prevMeaningfulIndex);
                    if ($tokens[$prevMeaningfulIndex]->isGivenKind(T_DECLARE)) {
                        continue;
                    }
                }
            }

            // fix white space after operator
            if ($tokens[$index + 1]->isWhitespace()) {
                $content = $tokens[$index + 1]->getContent();
                if (' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getNextNonWhitespace($index + 1)]->isComment()) {
                    $tokens[$index + 1]->setContent(' ');
                }
            } else {
                $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
            }

            // fix white space before operator
            if ($tokens[$index - 1]->isWhitespace()) {
                $content = $tokens[$index - 1]->getContent();
                if (' ' !== $content && false === strpos($content, "\n") && !$tokens[$tokens->getPrevNonWhitespace($index - 1)]->isComment()) {
                    $tokens[$index - 1]->setContent(' ');
                }
            } else {
                $tokens->insertAt($index, new Token(array(T_WHITESPACE, ' ')));
            }

            --$index; // skip check for binary operator on the whitespace token that is fixed.
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Binary operators should be surrounded by at least one space.';
    }
}
