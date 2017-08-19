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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer
 */
final class NoUnneededCurlyBracesFixerTest extends AbstractFixerTestCase
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
            'simple sample, last token candidate' => [
                '<?php  echo 1;',
                '<?php { echo 1;}',
            ],
            'minimal sample, first token candidate' => [
                '<?php  // {}',
                '<?php {} // {}',
            ],
            [
                '<?php
                      echo 0;   //
                    echo 1;
                    switch($a) {
                        case 2: echo 3; break;
                    }
                    echo 4;  echo 5; //
                ',
                '<?php
                    { { echo 0; } } //
                    {echo 1;}
                    switch($a) {
                        case 2: {echo 3; break;}
                    }
                    echo 4; { echo 5; }//
                ',
            ],
            'no fixes' => [
                '<?php
                    echo ${$a};
                    echo $a{1};

                    foreach($a as $b){}
                    while($a){}
                    do {} while($a);

                    if ($c){}
                    if ($c){}else{}
                    if ($c){}elseif($d){}
                    if ($c) {}elseif($d)/**  */{ } else/**/{  }

                    try {} catch(\Exception $e) {}

                    function test(){}
                    $a = function() use ($c){};

                    class A extends B {}
                    interface D {}
                    trait E {}
                ',
            ],
            'no fixes II' => [
                '<?php
                declare(ticks=1) {
                // entire script here
                }
                #',
            ],
            'no fix catch/try/finally' => [
                '<?php
                    try {

                    } catch(\Exception $e) {

                    } finally {

                    }
                ',
            ],
            'no fix namespace block' => [
                '<?php
                    namespace {
                    }
                    namespace A {
                    }
                    namespace A\B {
                    }
                ',
            ],
        ];
    }

    /**
     * @requires PHP 7
     *
     * @param string $expected
     *
     * @dataProvider provideNoFixCases7
     */
    public function testNoFix7($expected)
    {
        $this->doTest($expected);
    }

    public function provideNoFixCases7()
    {
        return [
            [
                '<?php
                    use some\a\{ClassA, ClassB, ClassC as C};
                    use function some\a\{fn_a, fn_b, fn_c};
                    use const some\a\{ConstA, ConstB, ConstC};
                    use some\x\{ClassB, function CC as C, function D, const E, function A\B};
                    class Foo
                    {
                        public function getBar(): array
                        {
                        }
                    }
                ',
            ],
        ];
    }
}
