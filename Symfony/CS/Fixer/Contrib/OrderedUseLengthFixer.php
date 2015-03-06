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

use Symfony\CS\AbstractOrderedUseFixer;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Graham Campbell <graham@mineuk.com>
 */
class OrderedUseLengthFixer extends AbstractOrderedUseFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ordering use statements by length, then alphabetically for those of the same length.';
    }

    /**
     * This method is used for sorting the uses in a namespace.
     *
     * @param string[] $first
     * @param string[] $second
     *
     * @internal
     */
    public static function sortingCallBack(array $first, array $second)
    {
        $la = strlen($first[0]);
        $lb = strlen($second[0]);

        if ($la === $lb) {
            return static::sortAlphabetically($first[0], $second[0]);
        }

        return $la - $lb;
    }
}
