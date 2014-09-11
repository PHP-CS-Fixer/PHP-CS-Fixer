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
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.3, ¶4.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class VisibilityFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $elements = $tokens->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $element) {
            if ('method' === $element['type']) {
                $this->applyAttribs($tokens, $index, $tokens->grabAttribsBeforeMethodToken($index));

                // force whitespace between function keyword and function name to be single space char
                $tokens[++$index]->content = ' ';
            } elseif ('property' === $element['type']) {
                $prevIndex = $tokens->getPrevTokenOfKind($index, array(';', ','));
                $nextIndex = $tokens->getNextTokenOfKind($index, array(';', ','));

                if (
                    (!$prevIndex || ',' !== $tokens[$prevIndex]->content) &&
                    (!$nextIndex || ',' !== $tokens[$nextIndex]->content)
                ) {
                    $this->applyAttribs($tokens, $index, $tokens->grabAttribsBeforePropertyToken($index));
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * Apply token attributes.
     * Token at given index is prepended by attributes.
     *
     * @param Tokens $tokens  Tokens collection
     * @param int    $index   token index
     * @param array  $attribs array of token attributes
     */
    private function applyAttribs(Tokens $tokens, $index, array $attribs)
    {
        $toInsert = array();

        foreach ($attribs as $attrib) {
            if (null !== $attrib && '' !== $attrib->content) {
                $toInsert[] = $attrib;
                $toInsert[] = new Token(array(T_WHITESPACE, ' '));
            }
        }

        if (!empty($toInsert)) {
            $tokens->insertAt($index, $toInsert);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.';
    }
}
