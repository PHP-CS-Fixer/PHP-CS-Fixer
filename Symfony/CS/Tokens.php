<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Tokens extends \SplFixedArray
{
    /**
     * Create token collection directly from code.
     *
     * @param  array  $array       the array to import
     * @param  bool   $saveIndexes save the numeric indexes used in the original array, default is yes
     * @return Tokens
     */
     public static function fromArray($array, $saveIndexes = null)
     {
        $tokens = new Tokens(count($array));

        if (null === $saveIndexes || $saveIndexes) {
            foreach ($array as $key => $val) {
                $tokens[$key] = $val;
            }

            return $tokens;
        }

        $index = 0;

        foreach ($array as $val) {
            $tokens[$index++] = $val;
        }

        return $tokens;
     }

    /**
     * Create token collection directly from code.
     *
     * @param  string $code PHP code
     * @return Tokens
     */
    public static function fromCode($code)
    {
        return static::fromArray(token_get_all($code));
    }
}
