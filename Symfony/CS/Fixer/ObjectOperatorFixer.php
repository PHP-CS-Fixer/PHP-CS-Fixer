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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ObjectOperatorFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] there should not be space before or after T_OBJECT_OPERATOR
        $previousToken = null;
        $tokens = token_get_all($content);
        $newTokens = array();
        for ($i = 0, $max = count($tokens); $i < $max; $i++) {
            if (is_array($tokens[$i])) {
                if (T_OBJECT_OPERATOR === $tokens[$i][0]) {
                    $last = count($newTokens) - 1;
                    if (isset($newTokens[$last]) && $this->isWhitespace($newTokens[$last])) {
                        // check that the previous one is a string (not a comment)
                        if (isset($newTokens[$last - 1]) && is_array($newTokens[$last - 1]) && T_VARIABLE === $newTokens[$last - 1][0]) {
                            array_pop($newTokens);
                        }
                    }
                    $newTokens[] = $tokens[$i];
                    if ($i + 1 < $max && $this->isWhitespace($tokens[$i + 1])) {
                        $i++;
                    }
                } else {
                    $newTokens[] = $tokens[$i];
                }
            } else {
                $newTokens[] = $tokens[$i];
            }
        }

        $content = '';
        foreach ($newTokens as $newToken) {
            if (is_array($newToken)) {
                $content .= $newToken[1];
            } else {
                $content .= $newToken;
            }
        }

        return $content;
    }

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'object_operator';
    }

    public function getDescription()
    {
        return 'There should not be space before or after object T_OBJECT_OPERATOR.';
    }

    private function isWhitespace($token)
    {
        return
            (is_string($token) && '' === trim($token, " \t"))
                ||
            (is_array($token) && T_WHITESPACE === $token[0] && '' === trim($token[1], " \t"))
        ;
    }
}
