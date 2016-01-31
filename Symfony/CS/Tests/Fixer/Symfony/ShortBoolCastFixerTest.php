<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class ShortBoolCastFixerTest extends AbstractFixerTestBase
{
    /**
     * * @dataProvider provideCases
     */
    public function testFixer($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
            $c = // lala
// cc
(bool)$content;',
                '<?php
            $c = ! // lala
                // cc
            !$content;',
            ),
            array(
                '<?php
$a = \'0\';
$b = /*

    */(bool)$a;',
                '<?php
$a = \'0\';
$b = !/*

    */!$a;',
            ),
            array(
                '<?php
function foo($a, $b) {
    $c = (bool)$a;
    $d = !$a;
    $d1 = !  $a;
    $d2 =    !$a;
    $b = !(!$foo);
    echo \'!!\'; // !! ! !
    $c = (bool) $b;
    $e = (bool) $d1;
    return (bool) $a;
}
                ',
                '<?php
function foo($a, $b) {
    $c = !!$a;
    $d = !$a;
    $d1 = !  $a;
    $d2 =    !$a;
    $b = !(!$foo);
    echo \'!!\'; // !! ! !
    $c = ! ! $b;
    $e = !


    ! $d1;
    return !! $a;
}
                ',
            ),
        );
    }
}
