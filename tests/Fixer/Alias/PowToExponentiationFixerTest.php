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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\Alias\PowToExponentiationFixer
 */
final class PowToExponentiationFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php 1**2;',
                '<?php pow(1,2);',
            ],
            [
                '<?php 1**2?>',
                '<?php pow(1,2)?>',
            ],
            [
                '<?php 1.2**2.3;',
                '<?php pow(1.2,2.3);',
            ],
            [
                '<?php echo (-2)** 3;',
                '<?php echo pow(-2, 3);',
            ],
            [
                '<?php echo (-2)**( -3);',
                '<?php echo pow(-2, -3);',
            ],
            [
                '<?php echo (-2)**( 1-3);',
                '<?php echo pow(-2, 1-3);',
            ],
            [
                '<?php echo (-2)**( -1-3);',
                '<?php echo pow(-2, -1-3);',
            ],
            [
                '<?php $a = 3** +2;',
                '<?php $a = pow(3, +2);',
            ],
            [
                '<?php $a--**++$b;',
                '<?php pow($a--,++$b);',
            ],
            [
                '<?php 1//
                #
                **2/**/ /**  */;',
                '<?php pow(1//
                #
                ,2/**/ /**  */);',
            ],
            [
                '<?php /**/a(3/**/,4)**$pow;//pow(1,2);',
                '<?php pow/**/(a(3/**/,4),$pow);//pow(1,2);',
            ],
            [
                '<?php \a\pow(5,6);7**8?>',
                '<?php \a\pow(5,6);pow(7,8)?>',
            ],
            [
                '<?php (9**10)**(11**12);',
                '<?php pow(pow(9,10),pow(11,12));',
            ],
            [
                '<?php (1 + 2)**( 3 * 4);',
                '<?php pow(1 + 2, 3 * 4);',
            ],
            [
                '<?php ($b = 4)** 3;',
                '<?php pow($b = 4, 3);',
            ],
            [
                '<?php 13**14;',
                '<?php \pow(13,14);',
            ],
            [
                '<?php $a = 15 + (16** 17)** 18;',
                '<?php $a = 15 + \pow(\pow(16, 17), 18);',
            ],
            [
                '<?php $a = $b** $c($d + 1);',
                '<?php $a = pow($b, $c($d + 1));',
            ],
            [
                '<?php $a = ($a+$b)** ($c-$d);',
                '<?php $a = pow(($a+$b), ($c-$d));',
            ],
            [
                "<?php \$a = 2**( '1'.'2'). 2;",
                "<?php \$a = pow(2, '1'.'2'). 2;",
            ],
            [
                '<?php A::B** 2;\A\B\C::B** 2;',
                '<?php pow(A::B, 2);pow(\A\B\C::B, 2);',
            ],
            [
                '<?php $obj->{$bar}** $obj->{$foo};',
                '<?php pow($obj->{$bar}, $obj->{$foo});',
            ],
            [
                '<?php echo ${$bar}** ${$foo};',
                '<?php echo pow(${$bar}, ${$foo});',
            ],
            [
                '<?php echo $a{1}** $b{2+5};',
                '<?php echo pow($a{1}, $b{2+5});',
            ],
            [
                '<?php echo $a[2^3+1]->test(1,2)** $b[2+$c];',
                '<?php echo pow($a[2^3+1]->test(1,2), $b[2+$c]);',
            ],
            [
                '<?php (int)"2"**(float)"3.0";',
                '<?php pow((int)"2",(float)"3.0");',
            ],
            [
                '<?php namespace\Foo::BAR** 2;',
                '<?php pow(namespace\Foo::BAR, 2);',
            ],
            [
                '<?php (-1)**( (-2)**( (-3)**( (-4)**( (-5)**( (-6)**( (-7)**( (-8)**( (-9)** 3))))))));',
                '<?php pow(-1, pow(-2, pow(-3, pow(-4, pow(-5, pow(-6, pow(-7, pow(-8, pow(-9, 3)))))))));',
            ],
            [
                '<?php
                    $z = 1**2;
                    $a = 1**( 2**( 3**( 4**( 5**( 6**( 7**( 8**( 9** 3))))))));
                    $b = 1**( 2**( 3**( 4**( 5**( 6**( 7**( 8**( 9** 3))))))));
                    $d = 1**2;
                ',
                '<?php
                    $z = pow(1,2);
                    $a = \pow(1, \poW(2, \pOw(3, \pOW(4, \Pow(5, \PoW(6, \POw(7, \POW(8, \pow(9, 3)))))))));
                    $b = \pow(1, \pow(2, \pow(3, \pow(4, \pow(5, \pow(6, \pow(7, \pow(8, \pow(9, 3)))))))));
                    $d = pow(1,2);
                ',
            ],
            [
                '<?php $b = 3** __LINE__;',
                '<?php $b = pow(3, __LINE__);',
            ],
            [
                '<?php
                    ($a-$b)**(
                    ($a-$b)**(
                    ($a-$b)**(
                    ($a-$b)**(
                    ($a-$b)**($a-$b)
                ))));',
                '<?php
                    pow($a-$b,
                    pow($a-$b,
                    pow($a-$b,
                    pow($a-$b,
                    pow($a-$b,$a-$b)
                ))));',
            ],
            [
                '<?php (-1)**( $a** pow(1,2,3, ($a-3)** 4));',
                '<?php pow(-1, pow($a, pow(1,2,3, pow($a-3, 4))));',
            ],
            [
                '<?php 1**2    /**/ ?>',
                '<?php pow(1,2)    /**/ ?>',
            ],
            [
                '<?php ($$a)**( $$b);',
                '<?php pow($$a, $$b);',
            ],
            [
                '<?php [1, 2, 3, 4][$x]** 2;',
                '<?php pow([1, 2, 3, 4][$x], 2);',
            ],
            [
                '<?php echo +$a** 2;',
                '<?php echo pow(+$a, 2);',
            ],
            [
                '<?php
                interface Test
                {
                    public function pow($a, $b);
                }',
            ],
            [
                '<?php
                interface Test
                {
                    public function &pow($a, $b);
                }',
            ],
        ];
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideNotFixCases
     */
    public function testNotFix($expected)
    {
        $this->doTest($expected);
    }

    public function provideNotFixCases()
    {
        return [
            [
                '<?php pow(); ++$a;++$a;++$a;++$a;++$a;++$a;// pow(1,2);',
            ],
            [
                '<?php pow(5); ++$a;++$a;++$a;++$a;++$a;++$a;# pow(1,2);',
            ],
            [
                '<?php pow(5,1,1); ++$a;++$a;++$a;++$a;++$a;++$a;/* pow(1,2); */',
            ],
            [
                '<?php \a\pow(4,3); ++$a;++$a;++$a;++$a;++$a;++$a;/** pow(1,2); */',
            ],
        ];
    }
}
