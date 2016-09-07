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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 */
class OperatorsSpacesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php $a +      /** */
                $b;',
                '<?php $a    +      /** */
                $b;',
            ),
            array(
                '<?php '.'
                    $a
                    + $b
                    + $d;
                ;',
                '<?php '.'
                    $a
                    +$b
                    +  $d;
                ;',
            ),
            array(
                '<?php
                    $a
               /***/ + $b
            /***/   + $d;
                ;',
                '<?php
                    $a
               /***/+   $b
            /***/   +$d;
                ;',
            ),
            array(
                '<?php $a + $b;',
                '<?php $a+$b;',
            ),
            array(
                '<?php 1 + $b;',
                '<?php 1+$b;',
            ),
            array(
                '<?php 0.2 + $b;',
                '<?php 0.2+$b;',
            ),
            array(
                '<?php $a[1] + $b;',
                '<?php $a[1]+$b;',
            ),
            array(
                '<?php FOO + $b;',
                '<?php FOO+$b;',
            ),
            array(
                '<?php foo() + $b;',
                '<?php foo()+$b;',
            ),
            array(
                '<?php ${"foo"} + $b;',
                '<?php ${"foo"}+$b;',
            ),
            array(
                '<?php $a & $b;',
                '<?php $a&$b;',
            ),
            array(
                '<?php $a &= $b;',
                '<?php $a&=$b;',
            ),
            array(
                '<?php $a &= $b;',
                '<?php $a &=$b;',
            ),
            array(
                '<?php $a &= $b;',
                '<?php $a&= $b;',
            ),
            array(
                '<?php $a &= $b;',
                '<?php $a  &=   $b;',
            ),
            array(
                '<?php $a &=
$b;',
            ),

            array(
                '<?php $a
&= $b;',
                '<?php $a
&=$b;',
            ),
            array(
                '<?php (1) and 2;',
                '<?php (1)and 2;',
            ),
            array(
                '<?php 1 or ($b - $c);',
                '<?php 1 or($b-$c);',
            ),
            array(
                '<?php "a" xor (2);',
                '<?php "a"xor(2);',
            ),
            array(
                '<?php $a * -$b;',
                '<?php $a*-$b;',
            ),
            array(
                '<?php $a = -2 / +5;',
                '<?php $a=-2/+5;',
            ),
            array(
                '<?php $a = &$b;',
                '<?php $a=&$b;',
            ),
            array(
                '<?php $a++ + $b;',
                '<?php $a+++$b;',
            ),
            array(
                '<?php __LINE__ - 1;',
                '<?php __LINE__-1;',
            ),
            array(
                '<?php `echo 1` + 1;',
                '<?php `echo 1`+1;',
            ),
            array(
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
            ),
            array(
                '<?php declare(ticks=1);',
            ),
            array(
                '<?php declare(ticks =  1);',
            ),
            array(
                '<?php $a = 1;declare(ticks =  1);$b = 1;',
                '<?php $a=1;declare(ticks =  1);$b=1;',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=>"c", );',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b" =>"c", );',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=> "c", );',
            ),
            array(
                '<?php $a = array("b"      =>      "c", );',
            ),
            array(
                '<?php $a = "c";',
                '<?php $a="c";',
            ),
            array(
                '<?php $a = "c";',
                '<?php $a ="c";',
            ),
            array(
                '<?php $a = "c";',
                '<?php $a= "c";',
            ),
            array(
                '<?php $a        =        "c";',
            ),
            array(
                '<?php $d =    $c + $a +     //
                $b;',
                '<?php $d =    $c+$a+     //
                $b;',
            ),
        );
    }

    /**
     * @dataProvider provideCases54
     * @requires PHP 5.4
     */
    public function testFix54($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases54()
    {
        return array(
            array(
                '<?php [1, 2] + [3, 4];',
                '<?php [1, 2]+[3, 4];',
            ),
            array(
                '<?php [1, 2] + [3, 4];',
                '<?php [1, 2]   +   [3, 4];',
            ),
            array(
                '<?php [1, 2] + //   '.'
                [3, 4];',
                '<?php [1, 2]   + //   '.'
                [3, 4];',
            ),
        );
    }
}
