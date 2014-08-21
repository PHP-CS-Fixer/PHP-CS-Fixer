<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\SpacesAroundOperatorsFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class SpacesAroundOperatorsFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();

        $this->assertSame($expected, $fixer->fix($this->getTestFile(), $input));
        $this->assertSame($expected, $fixer->fix($this->getTestFile(), $expected));
    }

    public function provideCases()
    {
        return array(
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
                '<?php $a &= $b;',
            ),
            array(
                '<?php $a  &=   $b;',
                '<?php $a  &=   $b;',
            ),
            array(
                '<?php $a &=
$b;',
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
                '<?php $a && $b;',
                '<?php $a&&$b;',
            ),
            array(
                '<?php $a || $b;',
                '<?php $a||$b;',
            ),
            array(
                '<?php $a .= $b;',
                '<?php $a.=$b;',
            ),
            array(
                '<?php $a /= $b;',
                '<?php $a/=$b;',
            ),
            array(
                '<?php $a == $b;',
                '<?php $a==$b;',
            ),
            array(
                '<?php $a >= $b;',
                '<?php $a>=$b;',
            ),
            array(
                '<?php $a === $b;',
                '<?php $a===$b;',
            ),
            array(
                '<?php $a != $b;',
                '<?php $a!=$b;',
            ),
            array(
                '<?php $a <> $b;',
                '<?php $a<>$b;',
            ),
            array(
                '<?php $a !== $b;',
                '<?php $a!==$b;',
            ),
            array(
                '<?php $a <= $b;',
                '<?php $a<=$b;',
            ),
            array(
                '<?php (1) and 2;',
                '<?php (1)and 2;',
            ),
            array(
                '<?php 1 or ($b-$c);',
                '<?php 1 or($b-$c);',
            ),
            array(
                '<?php "a" xor (2);',
                '<?php "a"xor(2);',
            ),
            array(
                '<?php $a -= $b;',
                '<?php $a-=$b;',
            ),
            array(
                '<?php $a %= $b;',
                '<?php $a%=$b;',
            ),
            array(
                '<?php $a *= $b;',
                '<?php $a*=$b;',
            ),
            array(
                '<?php $a |= $b;',
                '<?php $a|=$b;',
            ),
            array(
                '<?php $a += $b;',
                '<?php $a+=$b;',
            ),
            array(
                '<?php $a << $b;',
                '<?php $a<<$b;',
            ),
            array(
                '<?php $a <<= $b;',
                '<?php $a<<=$b;',
            ),
            array(
                '<?php $a >> $b;',
                '<?php $a>>$b;',
            ),
            array(
                '<?php $a >>= $b;',
                '<?php $a>>=$b;',
            ),
            array(
                '<?php $a ^= $b;',
                '<?php $a^=$b;',
            ),
            array(
                '<?php $a = $b;',
                '<?php $a=$b;',
            ),
            array(
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=>"c", );',
            ),
        );
    }

    private function getTestFile()
    {
        return new \SplFileInfo(__FILE__);
    }
}
