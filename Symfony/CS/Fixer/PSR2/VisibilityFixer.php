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
                $tokens->applyAttribs($index, $tokens->grabAttribsBeforeMethodToken($index));

                // force whitespace between function keyword and function name to be single space char
                $tokens[++$index]->content = ' ';
            } elseif ('property' === $element['type']) {
                $prevToken = $tokens->getPrevTokenOfKind($index, array(';', ','));
                $nextToken = $tokens->getNextTokenOfKind($index, array(';', ','));

                if (
                    (!$prevToken || ',' !== $prevToken->content) &&
                    (!$nextToken || ',' !== $nextToken->content)
                ) {
                    $tokens->applyAttribs($index, $tokens->grabAttribsBeforePropertyToken($index));
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.';
    }
}
