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

        $inClass = false;
        $curlyBracesLevel = 0;
        $bracesLevel = 0;

        foreach ($tokens as $index => $token) {
            if (!$inClass) {
                $inClass = Tokens::isClassy($token);
                continue;
            }

            if ('(' === $token) {
                ++$bracesLevel;
                continue;
            }

            if (')' === $token) {
                --$bracesLevel;
                continue;
            }

            if ('{' === $token || (is_array($token) && in_array($token[0], array(T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES, )))) {
                ++$curlyBracesLevel;
                continue;
            }

            if ('}' === $token) {
                --$curlyBracesLevel;

                if (0 === $curlyBracesLevel) {
                    $inClass = false;
                }

                continue;
            }

            if (1 !== $curlyBracesLevel || !is_array($token)) {
                continue;
            }

            if (T_VARIABLE === $token[0] && 0 === $bracesLevel) {
                // fix only if there is only one property defined in single statement
                if (
                    ',' !== $tokens->getPrevTokenOfKind($index, array(';', ',', )) &&
                    ',' !== $tokens->getNextTokenOfKind($index, array(';', ',', ))
                ) {
                    $tokens->applyAttribs($index, $tokens->grabAttribsBeforePropertyToken($index));
                }
                continue;
            }

            if (T_FUNCTION === $token[0]) {
                $tokens->applyAttribs($index, $tokens->grabAttribsBeforeMethodToken($index));

                // force whitespace between function keyword and function name to be single space char
                $tokens->next();
                $tokens[$tokens->key()] = ' ';
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
