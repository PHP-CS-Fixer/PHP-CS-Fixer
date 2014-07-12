<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class VisibilityFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $elements = $tokens->getClassyElements();

        foreach ($elements['methods'] as $index => $token) {
            $tokens->applyAttribs($index, $tokens->grabAttribsBeforeMethodToken($index));

            // force whitespace between function keyword and function name to be single space char
            $tokens[++$index]->content = ' ';
        }

        foreach ($elements['properties'] as $index => $token) {
            $prevToken = $tokens->getPrevTokenOfKind($index, array(';', ',', ));
            $nextToken = $tokens->getNextTokenOfKind($index, array(';', ',', ));

            if (
                (!$prevToken || ',' !== $prevToken->content) &&
                (!$nextToken || ',' !== $nextToken->content)
            ) {
                $tokens->applyAttribs($index, $tokens->grabAttribsBeforePropertyToken($index));
            }
        }

        return $tokens->generateCode();
    }

    public function getLevel()
    {
        // defined in PSR2 ¶4.3, ¶4.5
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'visibility';
    }

    public function getDescription()
    {
        return 'Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.';
    }
}
