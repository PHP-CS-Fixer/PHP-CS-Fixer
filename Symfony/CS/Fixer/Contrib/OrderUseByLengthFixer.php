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

use Symfony\CS\AbstractOrderedUseFixer;

/**
 * @author Panos Kyriakakis <panos@salix.gr>
 */
final class OrderUseByLengthFixer extends AbstractOrderedUseFixer
{
    /**
     * {@inheritdoc}
     */
    public function sortingCallBack(array $first, array $second)
    {
        $a = trim(preg_replace('%/\*(.*)\*/%s', '', $first[0]));
        $b = trim(preg_replace('%/\*(.*)\*/%s', '', $second[0]));

        // Replace backslashes by spaces before sorting for correct sort order
        $a = str_replace('\\', ' ', $a);
        $b = str_replace('\\', ' ', $b);

        $al = strlen($a);
        $bl = strlen($b);
        if ($al === $bl) {
            $out = strcasecmp($a, $b);
        } else {
            $out = $al > $bl ? 1 : -1;
        }

        return $out;
    }
}
