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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer
 */
final class BinaryOperatorSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideWithTabsCases
     */
    public function testWithTabs(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideWithTabsCases(): iterable
    {
        yield [
            <<<EOD
                <?php function myFunction() {
                \t\$foo         = 1;
                \t\$looooongVar = 2;
                \t\$middleVar   = 1;
                }
                EOD,
            <<<EOD
                <?php function myFunction() {
                \t\$foo= \t1;
                \t\$looooongVar\t  = 2;
                \t\$middleVar\t= 1;
                }
                EOD,
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<EOD
                <?php class A{
                public function myFunction() {
                \t \$foo         = 1;
                \t \$looooongVar = 2;
                \t \$middleVar   = 1;
                }
                }
                EOD,
            <<<EOD
                <?php class A{
                public function myFunction() {
                \t \$foo = 1;
                \t \$looooongVar = 2;
                \t \$middleVar = 1;
                }
                }
                EOD,
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN]],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideConfiguredCases
     */
    public function testConfigured(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideConfiguredCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                $this->a
                 = $this->b
                 = 1
                ;
                EOD,
            <<<'EOD'
                <?php
                $this->a
                = $this->b
                = 1
                ;
                EOD,
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php
                        $this->newName
                                = $this->path
                                = $this->randomName
                                = $this->remoteFile
                                = $this->tmpContent
                                = null;
                EOD,
            <<<'EOD'
                <?php
                        $this->newName
                                =     $this->path
                               =    $this->randomName
                              =   $this->remoteFile
                             =  $this->tmpContent
                            = null;
                EOD,
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php
                $a//
                     = 1;
                EOD."\n                ",
            <<<'EOD'
                <?php
                $a//
                     =  1;
                EOD."\n                ",
            ['operators' => ['=' => BinaryOperatorSpacesFixer::SINGLE_SPACE]],
        ];

        yield [
            <<<'EOD'
                <?php
                    $a =  1;
                    $b = 2;
                EOD."\n            ",
            <<<'EOD'
                <?php
                    $a =  1;
                    $b=2;
                EOD."\n            ",
            ['operators' => ['=' => BinaryOperatorSpacesFixer::AT_LEAST_SINGLE_SPACE]],
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [1 => 2];
                    foreach ([
                                1 => 2,
                                2 => 3,
                            ] as $k => $v) {
                        $var[] = [$i => $bar];
                    }
                EOD,
            <<<'EOD'
                <?php
                    $var = [1=>2];
                    foreach ([
                                1=> 2,
                                2   =>3,
                            ] as $k => $v) {
                        $var[] = [$i => $bar];
                    }
                EOD,
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php $a = array(
                                    1 => 2, 4 => 5,
                                    5 => 2, 6 => 5, 7 => 8, 9 => 10, 11 => 1222,
                                );
                EOD,
            <<<'EOD'
                <?php $a = array(
                                    1=>2, 4=>5,
                                    5=>2, 6 =>   5, 7=>8, 9=>10, 11=>1222,
                                );
                EOD,
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            '<?php $a = array(1 => 2, 4 => 5);',
            '<?php $a = array(1=>2, 4  =>  5);',
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            '<?php $a = array(1 => 2, 4 => 5 && $b, 5 => 5 && $b, 6 => 5 && $b, 7 => 5 && $b);',
            '<?php $a = array(1 => 2, 4 => 5&&$b, 5 => 5  &&  $b, 6 => 5&&  $b, 7 => 5  &&$b);',
            ['operators' => ['&&' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php
                                    [1 =>   "foo"];
                                    [2    => "foo"];
                                    [3 => "foo"];
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    [1 =>   "foo"];
                                    [2    =>"foo"];
                                    [3=>"foo"];
                EOD."\n                ",
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
        ];

        yield [
            <<<'EOD'
                <?php
                                    [1 => "foo"];
                                    [2 => "foo"];
                                    [3 => "foo"];
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    [1 =>   "foo"];
                                    [2    =>"foo"];
                                    [3=>"foo"];
                EOD."\n                ",
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            '<?php $a += 1;',
            '<?php $a+=1;',
            ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
        ];

        yield [
            '<?php $a += 1;',
            '<?php $a+=1;',
            ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            '<?php $a+=1;',
            null,
            ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN]],
        ];

        yield [
            <<<'EOD'
                <?php
                    $ade = $b !==   $a;
                    $b = $b   !==   $a;
                    $c = $b   !== $a;
                EOD."\n                ",
            <<<'EOD'
                <?php
                    $ade = $b!==   $a;
                    $b = $b!==   $a;
                    $c = $b!==$a;
                EOD."\n                ",
            ['operators' => ['!==' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
        ];

        yield [
            <<<'EOD'
                <?php
                    $aab = $b !== $e;
                    $b = $b   !== $c;
                    $c = $b   !== $d;
                EOD."\n                ",
            <<<'EOD'
                <?php
                    $aab = $b         !==$e;
                    $b = $b     !==$c;
                    $c = $b             !==$d;
                EOD."\n                ",
            ['operators' => ['!==' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php
                    $aaa*= 11;
                    $b  *= 21;
                    $c  *=31;

                    $d = $e and $f;
                    $d = $g   or    $h;
                EOD."\n                ",
            <<<'EOD'
                <?php
                    $aaa*= 11;
                    $b *= 21;
                    $c*=31;

                    $d = $e   and    $f;
                    $d = $g   or    $h;
                EOD."\n                ",
            [
                'operators' => [
                    'and' => BinaryOperatorSpacesFixer::SINGLE_SPACE,
                    '*=' => BinaryOperatorSpacesFixer::ALIGN,
                    'or' => null,
                ],
            ],
        ];

        yield [
            <<<'EOD'
                <?php
                    $abc = $b !== $a;
                    $b = $b   !== $a;
                    $c = $b   !== $a;
                EOD."\n                ",
            <<<'EOD'
                <?php
                    $abc = $b         !==    $a;
                    $b = $b     !==     $a;
                    $c = $b             !==    $a;
                EOD."\n                ",
            ['operators' => ['!==' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php $a = [
                                    1 => 2,
                                    2 => 3,
                                ];
                EOD,
            <<<'EOD'
                <?php $a = [
                                    1=>2,
                                    2  =>   3,
                                ];
                EOD,
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php
                                    [1 => "foo",
                                     2 => "foo"];
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    [1 =>   "foo",
                                     2   => "foo"];
                EOD."\n                ",
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php
                                    [1 => "foo"];
                                    $i += 1;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    [1 => "foo"];
                                    $i+= 1;
                EOD."\n                ",
            ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            <<<'EOD'
                <?php $a    =   1   +    2; $b = array(
                                    13 =>3,
                                    4  =>  3,
                                    5=>2,
                                );
                EOD,
            null,
            ['default' => null],
        ];

        yield [
            <<<'EOD'
                <?php $a = 1 + 2; $b = array(
                                    $øøø => $ø0ø0ø,
                                    $ø4  => $ø1ø1ø,
                                    $ø5  => $ø2ø2ø,
                                );
                                $a = 12 + 1;
                                $a = 13 + 41;
                EOD."\n                ",
            <<<'EOD'
                <?php $a    =   1   +    2; $b = array(
                                    $øøø =>$ø0ø0ø,
                                    $ø4  =>  $ø1ø1ø,
                                    $ø5=>$ø2ø2ø,
                                );
                                $a = 12   +  1;
                                $a = 13+41;
                EOD."\n                ",
            ['default' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL],
        ];

        yield 'do not align with nor touch strings' => [
            <<<'EOD'
                <?php
                                    \putenv("{$name}= {$value}");
                                $b                     = $c + 1;
                                                    $b = $c - 1;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    \putenv("{$name}= {$value}");
                                $b =$c+1;
                                                    $b =$c  -  1;
                EOD."\n                ",
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
        ];

        yield 'do not align with declare' => [
            <<<'EOD'
                <?php
                                    declare(ticks=1);
                                    $a = 1;
                                    $b = 1;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    declare(ticks=1);
                                    $a   = 1;
                                    $b              = 1;
                EOD."\n                ",
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield 'do not align with multibyte character in array key' => [
            <<<'EOD'
                <?php
                                    $map = [
                                        "ø" => "oe",
                                    ];
                EOD."\n                ",
            null,
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
        ];

        yield 'align correctly with multibyte characters in array key' => [
            <<<'EOD'
                <?php
                                    $inflect_male = array(
                                        "aitė\b" => "øasø",
                                        "ytė\b"  => "øisø",
                                        "iūtė\b" => "øiusø",
                                        "utė\b"  => array(
                                            "aitė\b" => "øas",
                                            "ytė\b"  => "øis",
                                            "iūtė\b" => $øøius,
                                            "utė\b"  => "us",
                                        ),
                                    );
                EOD,
            <<<'EOD'
                <?php
                                    $inflect_male = array(
                                        "aitė\b" => "øasø",
                                        "ytė\b" => "øisø",
                                        "iūtė\b" => "øiusø",
                                        "utė\b" => array(
                                            "aitė\b" => "øas",
                                            "ytė\b" => "øis",
                                            "iūtė\b" => $øøius,
                                            "utė\b"  =>     "us",
                                        ),
                                    );
                EOD,
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
        ];

        yield [
            <<<'EOD'
                <?php
                                    $foo = 1+$bar;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $foo  =  1 + $bar;
                EOD."\n                ",
            [
                'default' => BinaryOperatorSpacesFixer::NO_SPACE,
                'operators' => ['=' => BinaryOperatorSpacesFixer::SINGLE_SPACE],
            ],
        ];

        yield [
            <<<'EOD'
                <?php
                                    $foo = 1    +    $bar|$a;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $foo  =  1    +    $bar | $a;
                EOD."\n                ",
            [
                'default' => null,
                'operators' => [
                    '=' => BinaryOperatorSpacesFixer::SINGLE_SPACE,
                    '|' => BinaryOperatorSpacesFixer::NO_SPACE,
                ],
            ],
        ];

        yield [
            <<<'EOD'
                <?php
                                    $foo = $d #
                  |
                 #
                $a|         // foo
                $b#
                   |$d;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $foo           = $d #
                  |
                 #
                $a |         // foo
                $b#
                   | $d;
                EOD."\n                ",
            [
                'operators' => ['|' => BinaryOperatorSpacesFixer::NO_SPACE],
            ],
        ];

        yield [
            <<<'EOD'
                <?php declare(strict_types=1);
                $a = 1;
                echo 1 <=> 1;
                echo 1 <=> 2;
                echo 2 <=> 1;
                echo 2 <=> 1;

                $a = $a  ?? $b;
                $a = $ab ?? $b;
                $a = $ac ?? $b;
                $a = $ad ?? $b;
                $a = $ae ?? $b;

                EOD,
            <<<'EOD'
                <?php declare(strict_types=1);
                $a = 1;
                echo 1<=>1;
                echo 1 <=>2;
                echo 2<=> 1;
                echo 2  <=>   1;

                $a = $a ?? $b;
                $a = $ab   ?? $b;
                $a = $ac    ?? $b;
                $a = $ad  ?? $b;
                $a = $ae?? $b;

                EOD,
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE, '??' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield 'align array destructuring' => [
            <<<'EOD'
                <?php
                                    $c                 = [$d] = $e[1];
                                    function A(){}[$a] = $a[$c];
                                    $b                 = 1;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $c = [$d] = $e[1];
                                    function A(){}[$a] = $a[$c];
                                    $b = 1;
                EOD."\n                ",
            ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN]],
        ];

        yield 'align array destructuring with assignments' => [
            <<<'EOD'
                <?php
                                    $d = [
                                        "a" => $a,
                                        "b" => $b,
                                        "c" => $c
                                    ] = $array;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $d = [
                                        "a"=>$a,
                                        "b"   => $b,
                                        "c" =>   $c
                                    ] = $array;
                EOD."\n                ",
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield 'multiple exceptions catch, default config' => [
            '<?php try {} catch (A   |     B $e) {}',
        ];

        yield 'multiple exceptions catch, no space config' => [
            '<?php try {} catch (A   |     B $e) {}',
            null,
            ['operators' => ['|' => BinaryOperatorSpacesFixer::NO_SPACE]],
        ];

        yield 'multiple shifts in single line' => [
            <<<'EOD'
                <?php
                             $testA = $testB["abc"]    / $testC * 100;
                             $testD = $testE["abcdef"] / $testF * 100;

                             $testA = $testB["abc"]    / $testC * 10000000    * 100;
                             $testD = $testE["abcdef"] / $testF * 10000000000 * 100;
                EOD."\n            ",
            <<<'EOD'
                <?php
                             $testA = $testB["abc"]/$testC * 100;
                             $testD = $testE["abcdef"]/$testF * 100;

                             $testA = $testB["abc"]/$testC * 10000000 * 100;
                             $testD = $testE["abcdef"]/$testF * 10000000000 * 100;
                EOD."\n            ",
            ['default' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL],
        ];
    }

    /**
     * @dataProvider provideFixDefaultsCases
     */
    public function testFixDefaults(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixDefaultsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php $a +      /** */
                                $b;
                EOD,
            <<<'EOD'
                <?php $a    +      /** */
                                $b;
                EOD,
        ];

        yield [
            '<?php '.<<<'EOD'

                                    $a
                                    + $b
                                    + $d;
                                ;
                EOD,
            '<?php '.<<<'EOD'

                                    $a
                                    +$b
                                    +  $d;
                                ;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $a
                               /***/ + $b
                            /***/   + $d;
                                ;
                EOD,
            <<<'EOD'
                <?php
                                    $a
                               /***/+   $b
                            /***/   +$d;
                                ;
                EOD,
        ];

        yield [
            '<?php $a + $b;',
            '<?php $a+$b;',
        ];

        yield [
            '<?php 1 + $b;',
            '<?php 1+$b;',
        ];

        yield [
            '<?php 0.2 + $b;',
            '<?php 0.2+$b;',
        ];

        yield [
            '<?php $a[1] + $b;',
            '<?php $a[1]+$b;',
        ];

        yield [
            '<?php FOO + $b;',
            '<?php FOO+$b;',
        ];

        yield [
            '<?php foo() + $b;',
            '<?php foo()+$b;',
        ];

        yield [
            '<?php ${"foo"} + $b;',
            '<?php ${"foo"}+$b;',
        ];

        yield [
            '<?php $a & $b;',
            '<?php $a&$b;',
        ];

        yield [
            '<?php $a &= $b;',
            '<?php $a&=$b;',
        ];

        yield [
            '<?php $a &= $b;',
            '<?php $a &=$b;',
        ];

        yield [
            '<?php $a &= $b;',
            '<?php $a&= $b;',
        ];

        yield [
            '<?php $a &= $b;',
            '<?php $a  &=   $b;',
        ];

        yield [
            <<<'EOD'
                <?php $a &=
                $b;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php $a
                &= $b;
                EOD,
            <<<'EOD'
                <?php $a
                &=$b;
                EOD,
        ];

        yield [
            '<?php (1) and 2;',
            '<?php (1)and 2;',
        ];

        yield [
            '<?php 1 or ($b - $c);',
            '<?php 1 or($b-$c);',
        ];

        yield [
            '<?php "a" xor (2);',
            '<?php "a"xor(2);',
        ];

        yield [
            '<?php $a * -$b;',
            '<?php $a*-$b;',
        ];

        yield [
            '<?php $a = -2 / +5;',
            '<?php $a=-2/+5;',
        ];

        yield [
            '<?php $a = &$b;',
            '<?php $a=&$b;',
        ];

        yield [
            '<?php $a++ + $b;',
            '<?php $a+++$b;',
        ];

        yield [
            '<?php __LINE__ - 1;',
            '<?php __LINE__-1;',
        ];

        yield [
            '<?php `echo 1` + 1;',
            '<?php `echo 1`+1;',
        ];

        yield [
            '<?php function foo(&$a, array &$b, Bar &$c) {}',
        ];

        yield [
            <<<'EOD'
                <?php $a = 1 //
                                    || 2;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php $a =
                                    2;
                EOD,
        ];

        yield [
            '<?php declare(ticks=1);',
        ];

        yield [
            '<?php declare(ticks =  1);',
        ];

        yield [
            '<?php $a = 1;declare(ticks =  1);$b = 1;',
            '<?php $a=1;declare(ticks =  1);$b=1;',
        ];

        yield [
            '<?php $a = array("b" => "c", );',
            '<?php $a = array("b"=>"c", );',
        ];

        yield [
            '<?php $a = array("b" => "c", );',
            '<?php $a = array("b" =>"c", );',
        ];

        yield [
            '<?php $a = array("b" => "c", );',
            '<?php $a = array("b"=> "c", );',
        ];

        yield [
            '<?php [1, 2] + [3, 4];',
            '<?php [1, 2]+[3, 4];',
        ];

        yield [
            '<?php [1, 2] + [3, 4];',
            '<?php [1, 2]   +   [3, 4];',
        ];

        yield [
            '<?php [1, 2] + //   '.<<<'EOD'

                                [3, 4];
                EOD,
            '<?php [1, 2]   + //   '.<<<'EOD'

                                [3, 4];
                EOD,
        ];

        yield [
            '<?php $a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;',
            '<?php $a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;',
        ];

        yield [
            <<<'EOD'
                <?php
                $c =
                $a
                +
                $b;

                EOD,
        ];

        yield ['<a href="test-<?=$path?>-<?=$id?>.html">Test</a>'];

        yield 'reference in functions declarations' => [
            <<<'EOD'
                <?php
                                function a(string &$x) { return $x + 1; };
                                $b = function (string &$x) { return $x + 1; };
                                $c = fn (string &$x) => $x + 1;
                EOD."\n            ",
        ];
    }

    /**
     * @dataProvider provideUnalignEqualsCases
     */
    public function testUnalignEquals(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideUnalignEqualsCases(): iterable
    {
        yield [
            '<?php $a = "c"?>',
            '<?php $a="c"?>',
        ];

        yield [
            '<?php $a = "c";',
            '<?php $a ="c";',
        ];

        yield [
            '<?php $a = "c";',
            '<?php $a= "c";',
        ];

        yield [
            <<<'EOD'
                <?php $d = $c + $a/**/ +     //
                                $b;
                EOD,
            <<<'EOD'
                <?php $d =    $c+$a/**/+     //
                                $b;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = 1;
                    $bbbb = '
                    $cccccccc = 3;
                    ';
                EOD,
            <<<'EOD'
                <?php
                    $a    = 1;
                    $bbbb = '
                    $cccccccc = 3;
                    ';
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $ccc = 1;
                    $bb = 1;
                    $a = 1;

                    /*
                    Others alignments
                     */
                    $a[$b = 1] = 1;
                    $ab[$bc = 1] = 1;
                    $abc[$bcd = 1] = 1;
                    $a[$b] = 1;
                    $ab[$bc] = 1;
                    $abc[$bcd] = 1;

                    if ($a = 1) {
                        $ccc = 1;
                        $bb = 1;
                        $a = 1;
                    }

                    function a($a = 1, $b = 2, $c = 3)
                    {
                        $a[$b = 1] = 1;
                        $ab[$bc = 1] = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    function b(
                        $a = 1,
                        $bbb = 2,
                        $c = 3
                    ) {
                        $a[$b = 1] = 1;
                        $ab[$bc = 1] = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    while (false) {
                        $aa = 2;
                        $a[$b] = array();
                    }

                    for ($i = 0; $i < 10; $i++) {
                        $aa = 2;
                        $a[$b] = array(12);
                    }
                EOD,
            <<<'EOD'
                <?php
                    $ccc = 1;
                    $bb  = 1;
                    $a   = 1;

                    /*
                    Others alignments
                     */
                    $a[$b = 1]     = 1;
                    $ab[$bc = 1]   = 1;
                    $abc[$bcd = 1] = 1;
                    $a[$b]         = 1;
                    $ab[$bc]       = 1;
                    $abc[$bcd]     = 1;

                    if ($a = 1) {
                        $ccc = 1;
                        $bb  = 1;
                        $a   = 1;
                    }

                    function a($a = 1, $b = 2, $c = 3)
                    {
                        $a[$b = 1]     = 1;
                        $ab[$bc = 1]   = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    function b(
                        $a = 1,
                        $bbb = 2,
                        $c = 3
                    ) {
                        $a[$b = 1]     = 1;
                        $ab[$bc = 1]   = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    while (false) {
                        $aa    = 2;
                        $a[$b] = array();
                    }

                    for ($i = 0; $i < 10; $i++) {
                        $aa    = 2;
                        $a[$b] = array(12);
                    }
                EOD,
        ];
    }

    public function testWrongConfigItem(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[binary_operator_spaces\] Invalid configuration: The option "foo" does not exist\. Defined options are: "default", "operators"\.$/'
        );

        $this->fixer->configure(['foo' => true]);
    }

    public function testWrongConfigTypeForOperators(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '/^\[binary_operator_spaces\] Invalid configuration: The option "operators" with value true is expected to be of type "array", but is of type "(bool|boolean)"\.$/'
        );

        $this->fixer->configure(['operators' => true]);
    }

    public function testWrongConfigTypeForOperatorsKey(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[binary_operator_spaces\] Invalid configuration: Unexpected "operators" key, expected any of ".*", got "integer#123"\.$/');

        $this->fixer->configure(['operators' => [123 => 1]]);
    }

    public function testWrongConfigTypeForOperatorsKeyValue(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[binary_operator_spaces\] Invalid configuration: Unexpected value for operator "\+", expected any of ".*", got "string#abc"\.$/');

        $this->fixer->configure(['operators' => ['+' => 'abc']]);
    }

    /**
     * @dataProvider provideUnalignDoubleArrowCases
     */
    public function testUnalignDoubleArrow(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideUnalignDoubleArrowCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo" => "Bar",
                        "main" => array(
                            [
                                "baz" => "Test",
                                "bazaa" => $a->{"Test"},
                                "bazaa" => $a["Test"],
                                "bazaaaa" => b("Test"),
                            ]
                        ),
                        "bar" => array(),
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => array(
                            [
                                "baz"     => "Test",
                                "bazaa"   => $a->{"Test"},
                                "bazaa"   => $a["Test"],
                                "bazaaaa" => b("Test"),
                            ]
                        ),
                        "bar"  => array(),
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo" => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar" => array(),
                    ];
                    $data = array(
                        "foo" => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar" => array(),
                    );
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = /* Comment */ [$i => $bar];
                    }
                EOD,
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar"  => array(),
                    ];
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar"  => array(),
                    );
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = /* Comment */ [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo" => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar" => array(),
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar"  => array(),
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = array(
                        "foo" => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar" => array(),
                    );
                EOD,
            <<<'EOD'
                <?php
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar"  => array(),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = array(
                        "foo" => "Bar",
                        "main" => array(array("baz" => "Test")),
                        "bar" => array(),
                    );
                EOD,
            <<<'EOD'
                <?php
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array(array("baz" => "Test")),
                        "bar"  => array(),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = /* Comment */ [$i => $bar];
                    }
                EOD,
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $i  =>  $bar) {
                        $var[] = /* Comment */ [$i  =>  $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ([1 => 2] as $k => $v) {
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach (fncCall() as $k => $v){
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $bar) {
                        $var[] = [
                            $i => $bar,
                            $iaaa => $bar,
                        ];
                    }
                EOD,
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $bar) {
                        $var[] = [
                            $i    => $bar,
                            $iaaa => $bar,
                        ];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo" => "Bar",
                        "main" => [["baz" => "Test", "bar" => "Test2"]],
                        "bar" => [],
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [["baz" => "Test", "bar" => "Test2"]],
                        "bar"  => [],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = [
                        0 => 1,
                        10 /*Comment*/ => [
                            1 => 2,
                            22 => 3,
                        ],
                        100 => [
                            1 => 2,
                            22 => 3,
                        ]
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $a = [
                        0  => 1,
                        10 /*Comment*/ => [
                            1  => 2,
                            22 => 3,
                        ],
                        100 => [
                            1  => 2,
                            22 => 3,
                        ]
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = array(
                        0 => 1,
                        10 => array(
                            1 => 2,
                            22 => 3,
                        ),
                        100 => array(
                            1 => 2,
                            22 => 3,
                        )
                    );
                EOD,
            <<<'EOD'
                <?php
                    $a = array(
                        0  => 1,
                        10 => array(
                            1  => 2,
                            22 => 3,
                        ),
                        100 => array(
                            1  => 2,
                            22 => 3,
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $arr = array(
                        $a => 1,
                        $bbbb => '
                        $cccccccc = 3;
                        ',
                    );
                EOD,
            <<<'EOD'
                <?php
                    $arr = array(
                        $a    => 1,
                        $bbbb => '
                        $cccccccc = 3;
                        ',
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $arr = [
                        $a => 1,
                        $bbbb => '
                        $cccccccc = 3;
                        ',
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $arr = [
                        $a    => 1,
                        $bbbb => '
                        $cccccccc = 3;
                        ',
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    foreach($arr as $k => $v){
                        $arr = array($k => 1,
                            $a => 1,
                            $bbbb => '
                            $cccccccc = 3;
                            ',
                        );
                    }
                EOD,
            <<<'EOD'
                <?php
                    foreach($arr as $k => $v){
                        $arr = array($k => 1,
                            $a          => 1,
                            $bbbb       => '
                            $cccccccc = 3;
                            ',
                        );
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = array(
                        10 => 11,
                        20 => 22,
                        30 => 33,
                        40
                            =>
                                44,
                    );
                EOD,
            <<<'EOD'
                <?php
                    $a = array(
                        10    => 11,
                        20    => 22,
                        30=>33,
                        40
                            =>
                                44,
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        " " => "",    "\t" => "",
                        "\n" => "", "\r" => "",
                        "\0" => "", "\x0B" => "",
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        " "   => "",    "\t"    => "",
                        "\n"   => "", "\r"   => "",
                        "\0"  => "", "\x0B"    => "",
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return $this->grabAttribsBeforeToken(
                        $tokens,
                        $index,
                        $tokenAttribsMap,
                        array(
                            "abstract" => null,
                            "final" => null,
                            "visibility" => new Token(array(T_PUBLIC, "public")),
                            "static" => null,
                        )
                    );
                EOD,
            <<<'EOD'
                <?php
                    return $this->grabAttribsBeforeToken(
                        $tokens,
                        $index,
                        $tokenAttribsMap,
                        array(
                            "abstract"   => null,
                            "final"      => null,
                            "visibility" => new Token(array(T_PUBLIC, "public")),
                            "static"     => null,
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_0 => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_0 => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_0 => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_0    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $array = array(
                        "bazab" => b(array(
                            1 => 2,
                            5 => [
                                6 => 7,
                                8 => 9,
                            ],
                            3 => 4,
                            10 => 11,
                        )),
                    );
                EOD,
            <<<'EOD'
                <?php
                    $array = array(
                        "bazab" => b(array(
                            1 => 2,
                            5     => [
                                6 => 7,
                                8     => 9,
                            ],
                            3    => 4,
                            10      => 11,
                        )),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    Foo::test()->aaa(array(1 => 2))->bbb("a", "b");

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo() {
                        yield 1 => 2;
                    }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFixAlignEqualsCases
     */
    public function testFixAlignEquals(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN]]);
        $this->doTest($expected, $input);
    }

    public static function provideFixAlignEqualsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    $a    = 1;
                    $bbbb = '
                    $ddcccccc1 = 3;
                    ';
                EOD,
            <<<'EOD'
                <?php
                    $a = 1;
                    $bbbb = '
                    $ddcccccc1 = 3;
                    ';
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $ccc = 1;
                    $bb  = 1;
                    $a   = 1;

                    /*
                    Others alignments
                     */
                    $a[$b = 1]     = 1;
                    $ab[$bc = 1]   = 1;
                    $abc[$bcd = 1] = 1;
                    $a[$b]         = 1;
                    $ab[$bc]       = 1;
                    $abc[$bcd]     = 1;

                    if ($a = 1) {
                        $ccc = 1;
                        $bb  = 1;
                        $a   = 1;
                    }

                    function a($a = 1, $b = 2, $c = 3)
                    {
                        $a[$b = 1]     = 1;
                        $ab[$bc = 1]   = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    function b(
                        $a = 1,
                        $bbb = 2,
                        $c = 3
                    ) {
                        $a[$b = 1]     = 1;
                        $ab[$bc = 1]   = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    while ($i = 1) {
                        $aa    = 2;
                        $a[$b] = array();
                    }

                    for ($i = 0; $i < 10; $i++) {
                        $aa    = 2;
                        $a[$b] = array();
                    }

                    $z = 1;
                    switch($a = 0) {
                        case 1:
                            $b  = 1;
                            $cc = 3;
                        break;
                    }

                    foreach ($a as $b) {
                        $aa    = 2;
                        $a[$b] = array();
                    }

                    do {
                        $aa    = 23;
                        $a[$b] = array(66);
                    } while ($i = 1);
                    $a = 3;
                EOD."\n    ",
            <<<'EOD'
                <?php
                    $ccc = 1;
                    $bb = 1;
                    $a = 1;

                    /*
                    Others alignments
                     */
                    $a[$b = 1] = 1;
                    $ab[$bc = 1] = 1;
                    $abc[$bcd = 1] = 1;
                    $a[$b] = 1;
                    $ab[$bc] = 1;
                    $abc[$bcd] = 1;

                    if ($a = 1) {
                        $ccc = 1;
                        $bb = 1;
                        $a = 1;
                    }

                    function a($a = 1, $b = 2, $c = 3)
                    {
                        $a[$b = 1] = 1;
                        $ab[$bc = 1] = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    function b(
                        $a = 1,
                        $bbb = 2,
                        $c = 3
                    ) {
                        $a[$b = 1] = 1;
                        $ab[$bc = 1] = 1;
                        $abc[$bcd = 1] = 1;
                    }

                    while ($i = 1) {
                        $aa = 2;
                        $a[$b] = array();
                    }

                    for ($i = 0; $i < 10; $i++) {
                        $aa = 2;
                        $a[$b] = array();
                    }

                    $z = 1;
                    switch($a = 0) {
                        case 1:
                            $b = 1;
                            $cc = 3;
                        break;
                    }

                    foreach ($a as $b) {
                        $aa    = 2;
                        $a[$b] = array();
                    }

                    do {
                        $aa = 23;
                        $a[$b] = array(66);
                    } while ($i = 1);
                    $a = 3;
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                m(
                    function ()
                    {
                        $d["a"]   = 1;
                        $d["abc"] = 2;
                    }
                );

                EOD,
            <<<'EOD'
                <?php
                m(
                    function ()
                    {
                        $d["a"] = 1;
                        $d["abc"] = 2;
                    }
                );

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                class TaskObjectType
                {
                    public function configureOptions(OptionsResolver $resolver): void
                    {
                        $resolver->setDefaults(
                            [
                                "choices" => function (Options $options) {
                                    $choices   = TaskService::getFormMapperObjectList();
                                    $element   = null;
                                    $elementId = null;

                                    if (isset($options["task"]) && $options["task"]->getElement() === 42) {
                                        $element   = $options["task"]->getElement();
                                        $elementId = $options["task"]->getElementId();
                                    } elseif (isset($options["elementId"], $options["element"]) && $options["element"] === 42) {
                                        $element   = $options["element"];
                                        $elementId = $options["elementId"];
                                    };
                                },
                            ]
                        );
                    }
                }

                EOD,
            <<<'EOD'
                <?php

                class TaskObjectType
                {
                    public function configureOptions(OptionsResolver $resolver): void
                    {
                        $resolver->setDefaults(
                            [
                                "choices" => function (Options $options) {
                                    $choices = TaskService::getFormMapperObjectList();
                                    $element = null;
                                    $elementId = null;

                                    if (isset($options["task"]) && $options["task"]->getElement() === 42) {
                                        $element = $options["task"]->getElement();
                                        $elementId = $options["task"]->getElementId();
                                    } elseif (isset($options["elementId"], $options["element"]) && $options["element"] === 42) {
                                        $element = $options["element"];
                                        $elementId = $options["elementId"];
                                    };
                                },
                            ]
                        );
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                fn ($x = 1) => $x + 3;
                $f = 123;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (($c = count($array)) > 100) {
                    $_data = '100+';
                } elseif (($c = count($array)) > 0) {
                    $_data = '0+';
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if (($c = count($array)) > 100) {
                    $closure = fn ($x = 1) => $x + 3;
                } elseif (($c = count($array)) > 0) {
                    $closure = fn ($x = 1) => $x ** 3;
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $suppliersTitles          = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getTitle());
                $suppliersClassifications = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getClassification());

                EOD,
            <<<'EOD'
                <?php
                $suppliersTitles = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getTitle());
                $suppliersClassifications = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getClassification());

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $a              = [$s = 5, $d => 5, $c => 9,];
                $ab             = [$bc = 1];
                $someOtherArray = [$bcd = 1];
                $a              = [$b];
                $ab             = [$bc];
                $abc            = [$bcd];

                EOD,
            <<<'EOD'
                <?php
                $a = [$s = 5, $d => 5, $c => 9,];
                $ab = [$bc = 1];
                $someOtherArray = [$bcd = 1];
                $a = [$b];
                $ab = [$bc];
                $abc = [$bcd];

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $result = false;

                $callback = static function () use (&$result) {
                    $result = true;
                };

                $this->query = $this->db->prepare(static function ($db) {
                   $sql = "INSERT INTO {$db->protectIdentifiers($db->DBPrefix)} ("
                          . $db->protectIdentifiers("name") . ", "
                          . $db->protectIdentifiers("email") . ", "
                          . $db->protectIdentifiers("country");
                });

                $classSet = Closure::bind(function ($key, $value) {
                    $this->{$key} = $value;
                }, $classObj, $className);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $obj = new class() extends SomeClass {
                    public $someProperty = null;
                };

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $fabricator->setOverrides(["first" => "Bobby"], $persist = false);
                $bobbyUser = $fabricator->make();
                $bobbyUser = $fabricator->make();

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $a = 1; if (true) {
                $bbb = 1;
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $fabricator->setOverrides(
                ["first" => "Bobby"], $persist = false);
                $fabricator->setOverrides(["first" => "Bobby"], $persist = false
                );

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $start = (
                    $input["start"] !== "" && ($date = DateTime::parse($input["start"]))
                        ? $date->setTimezone("UTC")
                        : $date->setTimezone("Europe/London")
                );

                EOD,
        ];
    }

    /**
     * @dataProvider provideFixAlignDoubleArrowCases
     */
    public function testFixAlignDoubleArrow(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN]]);
        $this->doTest($expected, $input);
    }

    public static function provideFixAlignDoubleArrowCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case "prod":
                                        break;
                                }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $array = array(
                        "closure" => function ($param1, $param2) {
                            return;
                        }
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return new JsonResponse(array(
                        "result" => "OK",
                        "html"   => 1, /**/array(
                            "foo"    => "bar",
                            "foofoo" => array(
                                "a"  => 1,
                                "b"  => 2
                            )
                        ),)
                    );
                EOD,
            <<<'EOD'
                <?php
                    return new JsonResponse(array(
                        "result" => "OK",
                        "html" => 1, /**/array(
                            "foo" => "bar",
                            "foofoo" => array(
                                "a" => 1,
                                "b"  =>  2
                            )
                        ),)
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html"   => renderView("views/my_view.html.twig", array(
                            "foo"    => "bar",
                            "foofoo" => 43,
                        )),
                    ]);
                EOD,
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html" =>    renderView("views/my_view.html.twig", array(
                            "foo" => "bar",
                            "foofoo" => 43,
                        )),
                    ]);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html"   => renderView("views/my_view.html.twig", [
                            "foo"    => "bar",
                            "foofoo" => 42,
                        ]),
                        "baz" => "OK",
                    ]);
                EOD,
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html" =>    renderView("views/my_view.html.twig", [
                            "foo" =>   "bar",
                            "foofoo" =>    42,
                        ]),
                        "baz" => "OK",
                    ]);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => array(
                            [
                                "baz"     => "Test",
                                "bazaa"   => $a->{"Test"},
                                "bazaa"   => $a["Test"],
                                "bazaaaa" => b("Test"),
                            ]
                        ),
                        "bar"  => array(),
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => array(
                            [
                                "baz" => "Test",
                                "bazaa" => $a->{"Test"},
                                "bazaa" => $a["Test"],
                                "bazaaaa" => b("Test"),
                            ]
                        ),
                        "bar"  => array(),
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar"  => array(),
                    ];
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar"  => array(),
                    );
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = /* Comment */ [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar"  => array(),
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar"  => array(),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array(array("baz" => "Test")),
                        "bar"  => array(),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = /* Comment */ [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ([1 => 2] as $k => $v) {
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach (fncCall() as $k => $v){
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $bar) {
                        $var[] = [
                            $i    => $bar,
                            $iaaa => $bar,
                        ];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [["baz" => "Test", "bar" => "Test2"]],
                        "bar"  => [],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => ["baz" => "Test"],
                        "bar"  => [],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = [
                        0              => 1,
                        10 /*Comment*/ => [
                            1  => 2,
                            22 => 3,
                        ],
                        100 => [
                            1  => 2,
                            22 => 3,
                        ]
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $a = [
                        0  => 1,
                        10 /*Comment*/ => [
                            1  => 2,
                            22 => 3,
                        ],
                        100 => [
                            1  => 2,
                            22 => 3,
                        ]
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = array(
                        0   => 1,
                        10  => array(
                            1  => 2,
                            22 => 3,
                        ),
                        100 => array(
                            1  => 2,
                            22 => 3,
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $arr = array(
                        $a    => 1,
                        $bbbb => '
                        $cccccccc2 = 3;
                        ',
                    );
                EOD,
            <<<'EOD'
                <?php
                    $arr = array(
                        $a => 1,
                        $bbbb => '
                        $cccccccc2 = 3;
                        ',
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $arr = [
                        $a    => 1,
                        $bbbb => '
                        $cccccccc3 = 3;
                        ',
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $arr = [
                        $a => 1,
                        $bbbb => '
                        $cccccccc3 = 3;
                        ',
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    foreach($arr as $k => $v){
                        $arr = array($k => 1,
                            $a          => 1,
                            $bbbb       => '
                            $cccccccc4 = 3;
                            ',
                        );
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = array(
                        10    => 11,
                        20    => 22,
                        30    => 33,
                        40
                            =>
                                44,
                    );
                EOD,
            <<<'EOD'
                <?php
                    $a = array(
                        10    =>    11,
                        20  =>    22,
                        30=>33,
                        40
                            =>
                                44,
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        " "    => "",    "\t"    => "",
                        "\n"   => "", "\r"   => "",
                        "\0"   => "", "\x0B"    => "",
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        " "   => "",    "\t"    => "",
                        "\n"   => "", "\r"   => "",
                        "\0"  => "", "\x0B"    => "",
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return $this->grabAttribsBeforeToken(
                        $tokens,
                        $index,
                        $tokenAttribsMap,
                        array(
                            "abstract"   => null,
                            "final"      => null,
                            "visibility" => new Token(array(T_PUBLIC, "public")),
                            "static"     => null,
                        )
                    );
                EOD,
            <<<'EOD'
                <?php
                    return $this->grabAttribsBeforeToken(
                        $tokens,
                        $index,
                        $tokenAttribsMap,
                        array(
                            "abstract" => null,
                            "final" => null,
                            "visibility" => new Token(array(T_PUBLIC, "public")),
                            "static" => null,
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_1    => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_1    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_1 => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_1    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $array = array(
                        "bazab" => b(array(
                            1     => 2,
                            5     => [
                                6     => 7,
                                8     => 9,
                            ],
                            3       => 4,
                            10      => 11,
                        )),
                    );
                EOD,
            <<<'EOD'
                <?php
                    $array = array(
                        "bazab" => b(array(
                            1 => 2,
                            5     => [
                                6 => 7,
                                8     => 9,
                            ],
                            3    => 4,
                            10      => 11,
                        )),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    Foo::test()->aaa(array(1 => 2))->bbb("a", "b");

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $inflect_male = array(
                        "aitė\b" => "as",
                        "ytė\b"  => "is",
                        "iūtė\b" => "ius",
                        "utė\b"  => "us",
                    );
                EOD,
            <<<'EOD'
                <?php
                    $inflect_male = array(
                        "aitė\b" => "as",
                        "ytė\b" => "is",
                        "iūtė\b" => "ius",
                        "utė\b" => "us",
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                $formMapper
                                    ->add('foo', null, ['required' => false])
                                    ->add('dummy_field', null, ['required' => false])
                                ;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                $formMapper
                                    ->add('foo', null, array('required' => false))
                                    ->add('dummy_field', null, array('required' => false))
                                ;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(["server1" => $object], ["addedAt" => "DESC"], 5);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(["server2" => $object], ["checkedAt" => "desc"], 50);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array("server1" => $object), array("addedAt" => "DESC"), 5);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array("server2" => $object), array("checkedAt" => "desc"), 50);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy($foo[123]);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy($foo[123]);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy((1 + 2));
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy((1 + 2));
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array(1, 2));
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array(1, 2));
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php

                    function foo() {}

                    $bar = 42;

                    $foo = [
                        "test123" => "foo",
                        "foo"     => $bar[123],
                        "a"       => foo(),
                        "b"       => 1,
                    ];
                EOD."\n    ",
            <<<'EOD'
                <?php

                    function foo() {}

                    $bar = 42;

                    $foo = [
                        "test123" => "foo",
                        "foo" => $bar[123],
                        "a" => foo(),
                        "b" => 1,
                    ];
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_2    => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_2    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_2 => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_2    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_3    => array((1 + 11)=> "?", "description" => "unknown"),
                        self::STATUS_INVALID_3    => array((2 + 3)=> "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_3 => array((1+11)=> "?", "description" => "unknown"),
                        self::STATUS_INVALID_3    => array((2+3)=> "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_4    => ["symbol" => "?", "description" => "unknown"],
                        self::STATUS_INVALID_4    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_4 => ["symbol" => "?", "description" => "unknown"],
                        self::STATUS_INVALID_4    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_7    => [(1 + 11)=> "?", "description" => "unknown"],
                        self::STATUS_INVALID_7    => [(2 + 3)=> "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_7 => [(1+11)=> "?", "description" => "unknown"],
                        self::STATUS_INVALID_7    => [(2+3)=> "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $b = [1 => function() {
                    foreach([$a => 2] as $b) {
                        $bv = [
                            $b  => 2,
                            $cc => 3,
                        ];
                    }}, 2 => 3];

                EOD,
            <<<'EOD'
                <?php
                $b = [1 => function() {
                    foreach([$a => 2] as $b) {
                        $bv = [
                            $b => 2,
                            $cc => 3,
                        ];
                    }}, 2 => 3];

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function asd() {
                      return [
                          "this"    => fn () => false,
                          "is"      => fn () => false,
                          "an"      => fn () => false,
                          "example" => fn () => false,
                          "array"   => fn () => false,
                      ];
                }

                EOD,
            <<<'EOD'
                <?php
                function asd() {
                      return [
                          "this" => fn () => false,
                          "is" => fn () => false,
                          "an" => fn () => false,
                          "example" => fn () => false,
                          "array" => fn () => false,
                      ];
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                collect()
                    ->map(fn ($arg) => [])
                    ->keyBy(fn ($arg) => []);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($this->save([
                    "bar"       => "baz",
                    "barbarbar" => "baz",
                ])) {
                    // Do the work
                }

                EOD,
            <<<'EOD'
                <?php
                if ($this->save([
                    "bar" => "baz",
                    "barbarbar" => "baz",
                ])) {
                    // Do the work
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class test
                {
                    public function __construct()
                    {
                        $result = $this->test1(fn () => $this->test2($a));
                        foreach ($result as $k => $v)
                        {
                        }

                        $result = $this->test1(fn () => $this->test2($a, $b));
                        foreach ($result as $k => $v)
                        {
                        }
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $array = [
                    "foo"     => 123,
                    "longkey" => "test",
                    "baz"     => fn () => "value",
                ];

                EOD,
            <<<'EOD'
                <?php
                $array = [
                    "foo" => 123,
                    "longkey" => "test",
                    "baz" => fn () => "value",
                ];

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo () {
                    $this->query = $this->db->prepare(static fn ($db) => $db->table("user")->insert([
                        "name"    => "a",
                        "email"   => "b@example.com",
                        "country" => "JP",
                    ]));

                    foreach ($data as $name => $array) {
                        foreach ($array as $field => $value) {
                            yield $type => $case;
                        }
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                function foo () {
                    $this->query = $this->db->prepare(static fn ($db) => $db->table("user")->insert([
                        "name" => "a",
                        "email" => "b@example.com",
                        "country" => "JP",
                    ]));

                    foreach ($data as $name => $array) {
                        foreach ($array as $field => $value) {
                            yield $type => $case;
                        }
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function test()
                {
                    yield "null customer" => [
                        "expected"    => null,
                        "ourCustomer" => null,
                    ];
                    yield "no underlying user" => [
                        "expected"    => null,
                        "ourCustomer" => Customer::seed(),
                    ];
                }
                EOD."\n                ",
        ];
    }

    /**
     * @dataProvider provideFixAlignScopedDoubleArrowCases
     */
    public function testFixAlignScopedDoubleArrow(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_BY_SCOPE]]);
        $this->doTest($expected, $input);
    }

    public static function provideFixAlignScopedDoubleArrowCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                switch ($a) {
                                    case "prod":
                                        break;
                                }
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $array = array(
                        "closure" => function ($param1, $param2) {
                            return;
                        }
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return new JsonResponse(array(
                        "result" => "OK",
                        "html"   => 1, /**/array(
                            "foo"    => "bar",
                            "foofoo" => array(
                                "a"  => 1,
                                "b"  => 2
                            )
                        ),)
                    );
                EOD,
            <<<'EOD'
                <?php
                    return new JsonResponse(array(
                        "result" => "OK",
                        "html" => 1, /**/array(
                            "foo" => "bar",
                            "foofoo" => array(
                                "a" => 1,
                                "b"  =>  2
                            )
                        ),)
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html"   => renderView("views/my_view.html.twig", array(
                            "foo"    => "bar",
                            "foofoo" => 43,
                        )),
                    ]);
                EOD,
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html" =>    renderView("views/my_view.html.twig", array(
                            "foo" => "bar",
                            "foofoo" => 43,
                        )),
                    ]);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html"   => renderView("views/my_view.html.twig", [
                            "foo"    => "bar",
                            "foofoo" => 42,
                        ]),
                        "baz"    => "OK",
                    ]);
                EOD,
            <<<'EOD'
                <?php
                    return new JsonResponse([
                        "result" => "OK",
                        "html" =>    renderView("views/my_view.html.twig", [
                            "foo" =>   "bar",
                            "foofoo" =>    42,
                        ]),
                        "baz" => "OK",
                    ]);
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => array(
                            [
                                "baz"     => "Test",
                                "bazaa"   => $a->{"Test"},
                                "bazaa"   => $a["Test"],
                                "bazaaaa" => b("Test"),
                            ]
                        ),
                        "bar"  => array(),
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => array(
                            [
                                "baz" => "Test",
                                "bazaa" => $a->{"Test"},
                                "bazaa" => $a["Test"],
                                "bazaaaa" => b("Test"),
                            ]
                        ),
                        "bar"  => array(),
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar"  => array(),
                    ];
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar"  => array(),
                    );
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = /* Comment */ [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [array("baz" => "Test")],
                        "bar"  => array(),
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array("baz" => "Test"),
                        "bar"  => array(),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = array(
                        "foo"  => "Bar",
                        "main" => array(array("baz" => "Test")),
                        "bar"  => array(),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = /* Comment */ [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $i => $bar) {
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ([1 => 2] as $k => $v) {
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach (fncCall() as $k => $v){
                        $var[] = [$i => $bar];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $var = [];
                    foreach ($foo as $bar) {
                        $var[] = [
                            $i    => $bar,
                            $iaaa => $bar,
                        ];
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => [["baz" => "Test", "bar" => "Test2"]],
                        "bar"  => [],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $data = [
                        "foo"  => "Bar",
                        "main" => ["baz" => "Test"],
                        "bar"  => [],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = [
                        0              => 1,
                        10 /*Comment*/ => [
                            1  => 2,
                            22 => 3,
                        ],
                        100            => [
                            1  => 2,
                            22 => 3,
                        ]
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $a = [
                        0  => 1,
                        10 /*Comment*/ => [
                            1  => 2,
                            22 => 3,
                        ],
                        100 => [
                            1  => 2,
                            22 => 3,
                        ]
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = array(
                        0   => 1,
                        10  => array(
                            1  => 2,
                            22 => 3,
                        ),
                        100 => array(
                            1  => 2,
                            22 => 3,
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $arr = array(
                        $a    => 1,
                        $bbbb => '
                        $cccccccc2 = 3;
                        ',
                    );
                EOD,
            <<<'EOD'
                <?php
                    $arr = array(
                        $a => 1,
                        $bbbb => '
                        $cccccccc2 = 3;
                        ',
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $arr = [
                        $a    => 1,
                        $bbbb => '
                        $cccccccc3 = 3;
                        ',
                    ];
                EOD,
            <<<'EOD'
                <?php
                    $arr = [
                        $a => 1,
                        $bbbb => '
                        $cccccccc3 = 3;
                        ',
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    foreach($arr as $k => $v){
                        $arr = array($k => 1,
                            $a          => 1,
                            $bbbb       => '
                            $cccccccc4 = 3;
                            ',
                        );
                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = array(
                        10    => 11,
                        20    => 22,
                        30    => 33,
                        40
                              =>
                                44,
                    );
                EOD,
            <<<'EOD'
                <?php
                    $a = array(
                        10    =>    11,
                        20  =>    22,
                        30=>33,
                        40
                            =>
                                44,
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        " "    => "",    "\t"    => "",
                        "\n"   => "", "\r"   => "",
                        "\0"   => "", "\x0B"    => "",
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        " "   => "",    "\t"    => "",
                        "\n"   => "", "\r"   => "",
                        "\0"  => "", "\x0B"    => "",
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return $this->grabAttribsBeforeToken(
                        $tokens,
                        $index,
                        $tokenAttribsMap,
                        array(
                            "abstract"   => null,
                            "final"      => null,
                            "visibility" => new Token(array(T_PUBLIC, "public")),
                            "static"     => null,
                        )
                    );
                EOD,
            <<<'EOD'
                <?php
                    return $this->grabAttribsBeforeToken(
                        $tokens,
                        $index,
                        $tokenAttribsMap,
                        array(
                            "abstract" => null,
                            "final" => null,
                            "visibility" => new Token(array(T_PUBLIC, "public")),
                            "static" => null,
                        )
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_1    => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_1    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_1 => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_1    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $array = array(
                        "bazab" => b(array(
                            1       => 2,
                            5       => [
                                6     => 7,
                                8     => 9,
                            ],
                            3       => 4,
                            10      => 11,
                        )),
                    );
                EOD,
            <<<'EOD'
                <?php
                    $array = array(
                        "bazab" => b(array(
                            1 => 2,
                            5     => [
                                6 => 7,
                                8     => 9,
                            ],
                            3    => 4,
                            10      => 11,
                        )),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    Foo::test()->aaa(array(1 => 2))->bbb("a", "b");

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $inflect_male = array(
                        "aitė\b" => "as",
                        "ytė\b"  => "is",
                        "iūtė\b" => "ius",
                        "utė\b"  => "us",
                    );
                EOD,
            <<<'EOD'
                <?php
                    $inflect_male = array(
                        "aitė\b" => "as",
                        "ytė\b" => "is",
                        "iūtė\b" => "ius",
                        "utė\b" => "us",
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                $formMapper
                                    ->add('foo', null, ['required' => false])
                                    ->add('dummy_field', null, ['required' => false])
                                ;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                $formMapper
                                    ->add('foo', null, array('required' => false))
                                    ->add('dummy_field', null, array('required' => false))
                                ;
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(["server1" => $object], ["addedAt" => "DESC"], 5);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(["server2" => $object], ["checkedAt" => "desc"], 50);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array("server1" => $object), array("addedAt" => "DESC"), 5);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array("server2" => $object), array("checkedAt" => "desc"), 50);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy($foo[123]);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy($foo[123]);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy((1 + 2));
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy((1 + 2));
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array(1, 2));
                    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array(1, 2));
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php

                    function foo() {}

                    $bar = 42;

                    $foo = [
                        "test123" => "foo",
                        "foo"     => $bar[123],
                        "a"       => foo(),
                        "b"       => 1,
                    ];
                EOD."\n    ",
            <<<'EOD'
                <?php

                    function foo() {}

                    $bar = 42;

                    $foo = [
                        "test123" => "foo",
                        "foo" => $bar[123],
                        "a" => foo(),
                        "b" => 1,
                    ];
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_2    => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_2    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_2 => array("symbol" => "?", "description" => "unknown"),
                        self::STATUS_INVALID_2    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_3    => array((1 + 11)=> "?", "description" => "unknown"),
                        self::STATUS_INVALID_3    => array((2 + 3)=> "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
            <<<'EOD'
                <?php
                    return array(
                        self::STATUS_UNKNOWN_3 => array((1+11)=> "?", "description" => "unknown"),
                        self::STATUS_INVALID_3    => array((2+3)=> "III", "description" => "invalid file syntax, file ignored"),
                    );
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_4    => ["symbol" => "?", "description" => "unknown"],
                        self::STATUS_INVALID_4    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_4 => ["symbol" => "?", "description" => "unknown"],
                        self::STATUS_INVALID_4    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_7    => [(1 + 11)=> "?", "description" => "unknown"],
                        self::STATUS_INVALID_7    => [(2 + 3)=> "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
            <<<'EOD'
                <?php
                    return [
                        self::STATUS_UNKNOWN_7 => [(1+11)=> "?", "description" => "unknown"],
                        self::STATUS_INVALID_7    => [(2+3)=> "III", "description" => "invalid file syntax, file ignored"],
                    ];
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $b = [1 => function() {
                    foreach([$a => 2] as $b) {
                        $bv = [
                            $b  => 2,
                            $cc => 3,
                        ];
                    }}, 2 => 3];

                EOD,
            <<<'EOD'
                <?php
                $b = [1 => function() {
                    foreach([$a => 2] as $b) {
                        $bv = [
                            $b => 2,
                            $cc => 3,
                        ];
                    }}, 2 => 3];

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function asd() {
                      return [
                          "this"    => fn () => false,
                          "is"      => fn () => false,
                          "an"      => fn () => false,
                          "example" => fn () => false,
                          "array"   => fn () => false,
                      ];
                }

                EOD,
            <<<'EOD'
                <?php
                function asd() {
                      return [
                          "this" => fn () => false,
                          "is" => fn () => false,
                          "an" => fn () => false,
                          "example" => fn () => false,
                          "array" => fn () => false,
                      ];
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                collect()
                    ->map(fn ($arg) => [])
                    ->keyBy(fn ($arg) => []);

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                if ($this->save([
                    "bar"       => "baz",
                    "barbarbar" => "baz",
                ])) {
                    // Do the work
                }

                EOD,
            <<<'EOD'
                <?php
                if ($this->save([
                    "bar" => "baz",
                    "barbarbar" => "baz",
                ])) {
                    // Do the work
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                class test
                {
                    public function __construct()
                    {
                        $result = $this->test1(fn () => $this->test2($a));
                        foreach ($result as $k => $v)
                        {
                        }

                        $result = $this->test1(fn () => $this->test2($a, $b));
                        foreach ($result as $k => $v)
                        {
                        }
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $array = [
                    "foo"     => 123,
                    "longkey" => "test",
                    "baz"     => fn () => "value",
                ];

                EOD,
            <<<'EOD'
                <?php
                $array = [
                    "foo" => 123,
                    "longkey" => "test",
                    "baz" => fn () => "value",
                ];

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo () {
                    $this->query = $this->db->prepare(static fn ($db) => $db->table("user")->insert([
                        "name"    => "a",
                        "email"   => "b@example.com",
                        "country" => "JP",
                    ]));

                    foreach ($data as $name => $array) {
                        foreach ($array as $field => $value) {
                            yield $type => $case;
                        }
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                function foo () {
                    $this->query = $this->db->prepare(static fn ($db) => $db->table("user")->insert([
                        "name" => "a",
                        "email" => "b@example.com",
                        "country" => "JP",
                    ]));

                    foreach ($data as $name => $array) {
                        foreach ($array as $field => $value) {
                            yield $type => $case;
                        }
                    }
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function test()
                {
                    yield "null customer" => [
                        "expected"    => null,
                        "ourCustomer" => null,
                    ];
                    yield "no underlying user" => [
                        "expected"    => null,
                        "ourCustomer" => Customer::seed(),
                    ];
                }
                EOD."\n                ",
        ];
    }

    public function testDoNotTouchEqualsAndArrowByConfig(): void
    {
        $this->fixer->configure(
            [
                'operators' => [
                    '=' => null,
                    '=>' => null,
                ],
            ]
        );

        $this->doTest(
            <<<'EOD'
                <?php
                                $a = 1;
                                $aa = 1;
                                $aaa   =  1;
                                $aaB  =  1;
                                array(
                                    1  => 5,
                                    2 => 4,
                                    3   => 3,
                                    4   =>   2,
                                    5 =>  1,
                                     6 => 7,
                                );
                EOD."\n            "
        );
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixPhp74Cases
     */
    public function testFixPhp74(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixPhp74Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    $a = fn() => null;
                                    $b = fn() => null;
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = fn()    =>      null;
                                    $b = fn()      =>  null;
                EOD."\n                ",
            ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
        ];

        yield [
            '<?php $a ??= 1;',
            '<?php $a??=1;',
            ['operators' => ['??=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testUnionTypesAreNotChanged(): void
    {
        $this->doTest(
            <<<'EOD'
                <?php
                            class Foo
                            {
                                private bool|int | string $prop;
                                public function bar(TypeA | TypeB|TypeC $x): TypeA|TypeB | TypeC|TypeD
                                {
                                }
                                public function baz(
                                    callable|array $a,
                                    array|callable $b,
                                ) {}
                                public function qux(
                                    bool|int | string &$reference
                                ) {}
                                public function quux(): static| TypeA {}
                            }
                EOD
        );
    }

    /**
     * @requires PHP 8.1
     */
    public function testIntersectionTypesAreNotChanged(): void
    {
        $this->doTest(
            <<<'EOD'
                <?php
                            class Foo
                            {
                                private TypeA&TypeB & TypeC $prop;
                                public function bar(TypeA & TypeB&TypeC $x): TypeA&TypeB & TypeC&TypeD
                                {
                                }
                                public function baz(
                                    Countable&Traversable $a,
                                    Traversable&Countable $b,
                                ) {}
                            }
                EOD
        );
    }
}
