<?php

declare(strict_types=1);

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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer
 */
final class NoUnneededCurlyBracesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield from [
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

        yield 'provideNoFix7Cases' => [
            '<?php
                    use some\a\{ClassA, ClassB, ClassC as C};
                    use function some\a\{fn_a, fn_b, fn_c};
                    use const some\a\{ConstA, ConstB, ConstC};
                    use some\x\{ClassD, function CC as C, function D, const E, function A\B};
                    class Foo
                    {
                        public function getBar(): array
                        {
                        }
                    }
                ',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPre80Cases(): iterable
    {
        yield 'no fixes, offset access syntax with curly braces' => [
            '<?php
                    echo ${$a};
                    echo $a{1};
                ',
        ];
    }

    /**
     * @dataProvider provideFixNamespaceCases
     */
    public function testFixNamespace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['namespaces' => true]);
        $this->doTest($expected, $input);
    }

    public function provideFixNamespaceCases(): iterable
    {
        yield [
            '<?php
namespace Foo;
    function Bar(){}

',
            '<?php
namespace Foo {
    function Bar(){}
}
',
        ];

        yield [
            '<?php
            namespace A5 {
                function AA(){}
            }
            namespace B6 {
                function BB(){}
            }',
        ];

        yield [
            '<?php
            namespace Foo7;
                function Bar(){}
            ',
            '<?php
            namespace Foo7 {
                function Bar(){}
            }',
        ];

        yield [
            '<?php
            namespace Foo8\\A;
                function Bar(){}
             ?>',
            "<?php
            namespace Foo8\\A\t \t {
                function Bar(){}
            } ?>",
        ];
    }
}
