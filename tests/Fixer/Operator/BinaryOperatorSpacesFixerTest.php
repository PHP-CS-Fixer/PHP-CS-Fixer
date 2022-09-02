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

    public function provideWithTabsCases(): array
    {
        return [
            [
                "<?php function myFunction() {
\t\$foo         = 1;
\t\$looooongVar = 2;
\t\$middleVar   = 1;
}",
                "<?php function myFunction() {
\t\$foo= \t1;
\t\$looooongVar\t  = 2;
\t\$middleVar\t= 1;
}",
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                "<?php class A{
public function myFunction() {
\t \$foo         = 1;
\t \$looooongVar = 2;
\t \$middleVar   = 1;
}
}",
                "<?php class A{
public function myFunction() {
\t \$foo = 1;
\t \$looooongVar = 2;
\t \$middleVar = 1;
}
}",
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN]],
            ],
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

    public function provideConfiguredCases(): array
    {
        return [
            [
                '<?php
$this->a
 = $this->b
 = 1
;',
                '<?php
$this->a
= $this->b
= 1
;',
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php
        $this->newName
                = $this->path
                = $this->randomName
                = $this->remoteFile
                = $this->tmpContent
                = null;',
                '<?php
        $this->newName
                =     $this->path
               =    $this->randomName
              =   $this->remoteFile
             =  $this->tmpContent
            = null;',
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php
$a//
     = 1;
                ',
                '<?php
$a//
     =  1;
                ',
                ['operators' => ['=' => BinaryOperatorSpacesFixer::SINGLE_SPACE]],
            ],
            [
                '<?php
    $var = [1 => 2];
    foreach ([
                1 => 2,
                2 => 3,
            ] as $k => $v) {
        $var[] = [$i => $bar];
    }',
                '<?php
    $var = [1=>2];
    foreach ([
                1=> 2,
                2   =>3,
            ] as $k => $v) {
        $var[] = [$i => $bar];
    }',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a = array(
                    1 => 2, 4 => 5,
                    5 => 2, 6 => 5, 7 => 8, 9 => 10, 11 => 1222,
                );',
                '<?php $a = array(
                    1=>2, 4=>5,
                    5=>2, 6 =>   5, 7=>8, 9=>10, 11=>1222,
                );',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a = array(1 => 2, 4 => 5);',
                '<?php $a = array(1=>2, 4  =>  5);',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a = array(1 => 2, 4 => 5 && $b, 5 => 5 && $b, 6 => 5 && $b, 7 => 5 && $b);',
                '<?php $a = array(1 => 2, 4 => 5&&$b, 5 => 5  &&  $b, 6 => 5&&  $b, 7 => 5  &&$b);',
                ['operators' => ['&&' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php
                    [1 =>   "foo"];
                    [2    => "foo"];
                    [3 => "foo"];
                ',
                '<?php
                    [1 =>   "foo"];
                    [2    =>"foo"];
                    [3=>"foo"];
                ',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
            ],
            [
                '<?php
                    [1 => "foo"];
                    [2 => "foo"];
                    [3 => "foo"];
                ',
                '<?php
                    [1 =>   "foo"];
                    [2    =>"foo"];
                    [3=>"foo"];
                ',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a += 1;',
                '<?php $a+=1;',
                ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
            ],
            [
                '<?php $a += 1;',
                '<?php $a+=1;',
                ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a+=1;',
                null,
                ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN]],
            ],
            [
                '<?php
    $ade = $b !==   $a;
    $b = $b   !==   $a;
    $c = $b   !== $a;
                ',
                '<?php
    $ade = $b!==   $a;
    $b = $b!==   $a;
    $c = $b!==$a;
                ',
                ['operators' => ['!==' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
            ],
            [
                '<?php
    $aab = $b !== $e;
    $b = $b   !== $c;
    $c = $b   !== $d;
                ',
                '<?php
    $aab = $b         !==$e;
    $b = $b     !==$c;
    $c = $b             !==$d;
                ',
                ['operators' => ['!==' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php
    $aaa*= 11;
    $b  *= 21;
    $c  *=31;

    $d = $e and $f;
    $d = $g   or    $h;
                ',
                '<?php
    $aaa*= 11;
    $b *= 21;
    $c*=31;

    $d = $e   and    $f;
    $d = $g   or    $h;
                ',
                [
                    'operators' => [
                        'and' => BinaryOperatorSpacesFixer::SINGLE_SPACE,
                        '*=' => BinaryOperatorSpacesFixer::ALIGN,
                        'or' => null,
                    ],
                ],
            ],
            [
                '<?php
    $abc = $b !== $a;
    $b = $b   !== $a;
    $c = $b   !== $a;
                ',
                '<?php
    $abc = $b         !==    $a;
    $b = $b     !==     $a;
    $c = $b             !==    $a;
                ',
                ['operators' => ['!==' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a = [
                    1 => 2,
                    2 => 3,
                ];',
                '<?php $a = [
                    1=>2,
                    2  =>   3,
                ];',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php
                    [1 => "foo",
                     2 => "foo"];
                ',
                '<?php
                    [1 =>   "foo",
                     2   => "foo"];
                ',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php
                    [1 => "foo"];
                    $i += 1;
                ',
                '<?php
                    [1 => "foo"];
                    $i+= 1;
                ',
                ['operators' => ['+=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a    =   1   +    2; $b = array(
                    13 =>3,
                    4  =>  3,
                    5=>2,
                );',
                null,
                ['default' => null],
            ],
            [
                '<?php $a = 1 + 2; $b = array(
                    $øøø => $ø0ø0ø,
                    $ø4  => $ø1ø1ø,
                    $ø5  => $ø2ø2ø,
                );
                $a = 12 + 1;
                $a = 13 + 41;
                ',
                '<?php $a    =   1   +    2; $b = array(
                    $øøø =>$ø0ø0ø,
                    $ø4  =>  $ø1ø1ø,
                    $ø5=>$ø2ø2ø,
                );
                $a = 12   +  1;
                $a = 13+41;
                ',
                ['default' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL],
            ],
            'do not align with nor touch strings' => [
                '<?php
                    \putenv("{$name}= {$value}");
                $b                     = $c + 1;
                                    $b = $c - 1;
                ',
                '<?php
                    \putenv("{$name}= {$value}");
                $b =$c+1;
                                    $b =$c  -  1;
                ',
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
            ],
            'do not align with declare' => [
                '<?php
                    declare(ticks=1);
                    $a = 1;
                    $b = 1;
                ',
                '<?php
                    declare(ticks=1);
                    $a   = 1;
                    $b              = 1;
                ',
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            'do not align with multibyte character in array key' => [
                '<?php
                    $map = [
                        "ø" => "oe",
                    ];
                ',
                null,
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
            ],
            'align correctly with multibyte characters in array key' => [
                '<?php
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
                    );',
                '<?php
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
                    );',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
            ],
            [
                '<?php
                    $foo = 1+$bar;
                ',
                '<?php
                    $foo  =  1 + $bar;
                ',
                [
                    'default' => BinaryOperatorSpacesFixer::NO_SPACE,
                    'operators' => ['=' => BinaryOperatorSpacesFixer::SINGLE_SPACE],
                ],
            ],
            [
                '<?php
                    $foo = 1    +    $bar|$a;
                ',
                '<?php
                    $foo  =  1    +    $bar | $a;
                ',
                [
                    'default' => null,
                    'operators' => [
                        '=' => BinaryOperatorSpacesFixer::SINGLE_SPACE,
                        '|' => BinaryOperatorSpacesFixer::NO_SPACE,
                    ],
                ],
            ],
            [
                '<?php
                    $foo = $d #
  |
 #
$a|         // foo
$b#
   |$d;
                ',
                '<?php
                    $foo           = $d #
  |
 #
$a |         // foo
$b#
   | $d;
                ',
                [
                    'operators' => ['|' => BinaryOperatorSpacesFixer::NO_SPACE],
                ],
            ],
            [
                '<?php declare(strict_types=1);
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
',
                '<?php declare(strict_types=1);
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
',
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE, '??' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            'align array destruction' => [
                '<?php
                    $c                 = [$d] = $e[1];
                    function A(){}[$a] = $a[$c];
                    $b                 = 1;
                ',
                '<?php
                    $c = [$d] = $e[1];
                    function A(){}[$a] = $a[$c];
                    $b = 1;
                ',
                ['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN]],
            ],
            'align array destruction with assignments' => [
                '<?php
                    $d = [
                        "a" => $a,
                        "b" => $b,
                        "c" => $c
                    ] = $array;
                ',
                '<?php
                    $d = [
                        "a"=>$a,
                        "b"   => $b,
                        "c" =>   $c
                    ] = $array;
                ',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            'multiple exceptions catch, default config' => [
                '<?php try {} catch (A   |     B $e) {}',
            ],
            'multiple exceptions catch, no space config' => [
                '<?php try {} catch (A   |     B $e) {}',
                null,
                ['operators' => ['|' => BinaryOperatorSpacesFixer::NO_SPACE]],
            ],
        ];
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFixDefaults(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php $a +      /** */
                $b;',
                '<?php $a    +      /** */
                $b;',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php $a + $b;',
                '<?php $a+$b;',
            ],
            [
                '<?php 1 + $b;',
                '<?php 1+$b;',
            ],
            [
                '<?php 0.2 + $b;',
                '<?php 0.2+$b;',
            ],
            [
                '<?php $a[1] + $b;',
                '<?php $a[1]+$b;',
            ],
            [
                '<?php FOO + $b;',
                '<?php FOO+$b;',
            ],
            [
                '<?php foo() + $b;',
                '<?php foo()+$b;',
            ],
            [
                '<?php ${"foo"} + $b;',
                '<?php ${"foo"}+$b;',
            ],
            [
                '<?php $a & $b;',
                '<?php $a&$b;',
            ],
            [
                '<?php $a &= $b;',
                '<?php $a&=$b;',
            ],
            [
                '<?php $a &= $b;',
                '<?php $a &=$b;',
            ],
            [
                '<?php $a &= $b;',
                '<?php $a&= $b;',
            ],
            [
                '<?php $a &= $b;',
                '<?php $a  &=   $b;',
            ],
            [
                '<?php $a &=
$b;',
            ],
            [
                '<?php $a
&= $b;',
                '<?php $a
&=$b;',
            ],
            [
                '<?php (1) and 2;',
                '<?php (1)and 2;',
            ],
            [
                '<?php 1 or ($b - $c);',
                '<?php 1 or($b-$c);',
            ],
            [
                '<?php "a" xor (2);',
                '<?php "a"xor(2);',
            ],
            [
                '<?php $a * -$b;',
                '<?php $a*-$b;',
            ],
            [
                '<?php $a = -2 / +5;',
                '<?php $a=-2/+5;',
            ],
            [
                '<?php $a = &$b;',
                '<?php $a=&$b;',
            ],
            [
                '<?php $a++ + $b;',
                '<?php $a+++$b;',
            ],
            [
                '<?php __LINE__ - 1;',
                '<?php __LINE__-1;',
            ],
            [
                '<?php `echo 1` + 1;',
                '<?php `echo 1`+1;',
            ],
            [
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
            ],
            [
                '<?php $a = 1 //
                    || 2;
                ',
            ],
            [
                '<?php $a =
                    2;',
            ],
            [
                '<?php declare(ticks=1);',
            ],
            [
                '<?php declare(ticks =  1);',
            ],
            [
                '<?php $a = 1;declare(ticks =  1);$b = 1;',
                '<?php $a=1;declare(ticks =  1);$b=1;',
            ],
            [
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=>"c", );',
            ],
            [
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b" =>"c", );',
            ],
            [
                '<?php $a = array("b" => "c", );',
                '<?php $a = array("b"=> "c", );',
            ],
            [
                '<?php [1, 2] + [3, 4];',
                '<?php [1, 2]+[3, 4];',
            ],
            [
                '<?php [1, 2] + [3, 4];',
                '<?php [1, 2]   +   [3, 4];',
            ],
            [
                '<?php [1, 2] + //   '.'
                [3, 4];',
                '<?php [1, 2]   + //   '.'
                [3, 4];',
            ],
            [
                '<?php $a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;$a = $b + $c;',
                '<?php $a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;$a=$b+$c;',
            ],
            [
                '<?php
$c =
$a
+
$b;
',
            ],
            ['<a href="test-<?=$path?>-<?=$id?>.html">Test</a>'],
        ];
    }

    /**
     * @dataProvider provideUnalignEqualsCases
     */
    public function testUnalignEquals(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideUnalignEqualsCases(): array
    {
        return [
            [
                '<?php $a = "c"?>',
                '<?php $a="c"?>',
            ],
            [
                '<?php $a = "c";',
                '<?php $a ="c";',
            ],
            [
                '<?php $a = "c";',
                '<?php $a= "c";',
            ],
            [
                '<?php $d = $c + $a/**/ +     //
                $b;',
                '<?php $d =    $c+$a/**/+     //
                $b;',
            ],
            [
                '<?php
    $a = 1;
    $bbbb = \'
    $cccccccc = 3;
    \';',
                '<?php
    $a    = 1;
    $bbbb = \'
    $cccccccc = 3;
    \';',
            ],
            [
                '<?php
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
    }',
                '<?php
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
    }',
            ],
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

    public function provideUnalignDoubleArrowCases(): array
    {
        return [
            [
                '<?php
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
    ];',
                '<?php
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
    ];',
            ],
            [
                '<?php
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
    }',
                '<?php
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
    }',
            ],
            [
                '<?php
    $data = [
        "foo" => "Bar",
        "main" => [array("baz" => "Test")],
        "bar" => array(),
    ];',
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => [array("baz" => "Test")],
        "bar"  => array(),
    ];',
            ],
            [
                '<?php
    $data = array(
        "foo" => "Bar",
        "main" => array("baz" => "Test"),
        "bar" => array(),
    );',
                '<?php
    $data = array(
        "foo"  => "Bar",
        "main" => array("baz" => "Test"),
        "bar"  => array(),
    );',
            ],
            [
                '<?php
    $data = array(
        "foo" => "Bar",
        "main" => array(array("baz" => "Test")),
        "bar" => array(),
    );',
                '<?php
    $data = array(
        "foo"  => "Bar",
        "main" => array(array("baz" => "Test")),
        "bar"  => array(),
    );',
            ],
            [
                '<?php
    $var = [];
    foreach ($foo as $i => $bar) {
        $var[] = /* Comment */ [$i => $bar];
    }',
                '<?php
    $var = [];
    foreach ($foo as $i  =>  $bar) {
        $var[] = /* Comment */ [$i  =>  $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach ($foo as $i => $bar) {
        $var[] = [$i => $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach ([1 => 2] as $k => $v) {
        $var[] = [$i => $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach (fncCall() as $k => $v){
        $var[] = [$i => $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach ($foo as $bar) {
        $var[] = [
            $i => $bar,
            $iaaa => $bar,
        ];
    }',
                '<?php
    $var = [];
    foreach ($foo as $bar) {
        $var[] = [
            $i    => $bar,
            $iaaa => $bar,
        ];
    }',
            ],
            [
                '<?php
    $data = [
        "foo" => "Bar",
        "main" => [["baz" => "Test", "bar" => "Test2"]],
        "bar" => [],
    ];',
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => [["baz" => "Test", "bar" => "Test2"]],
        "bar"  => [],
    ];',
            ],
            [
                '<?php
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
    ];',
                '<?php
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
    ];',
            ],
            [
                '<?php
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
    );',
                '<?php
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
    );',
            ],
            [
                '<?php
    $arr = array(
        $a => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    );',
                '<?php
    $arr = array(
        $a    => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    );',
            ],
            [
                '<?php
    $arr = [
        $a => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    ];',
                '<?php
    $arr = [
        $a    => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    ];',
            ],
            [
                '<?php
    foreach($arr as $k => $v){
        $arr = array($k => 1,
            $a => 1,
            $bbbb => \'
            $cccccccc = 3;
            \',
        );
    }',
                '<?php
    foreach($arr as $k => $v){
        $arr = array($k => 1,
            $a          => 1,
            $bbbb       => \'
            $cccccccc = 3;
            \',
        );
    }',
            ],
            [
                '<?php
    $a = array(
        10 => 11,
        20 => 22,
        30 => 33,
        40
            =>
                44,
    );',
                '<?php
    $a = array(
        10    => 11,
        20    => 22,
        30=>33,
        40
            =>
                44,
    );',
            ],
            [
                '<?php
    return array(
        " " => "",    "\t" => "",
        "\n" => "", "\r" => "",
        "\0" => "", "\x0B" => "",
    );',
                '<?php
    return array(
        " "   => "",    "\t"    => "",
        "\n"   => "", "\r"   => "",
        "\0"  => "", "\x0B"    => "",
    );',
            ],
            [
                '<?php
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
    );',
                '<?php
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
    );',
            ],
            [
                '<?php
    return array(
        self::STATUS_UNKNOWN_0 => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID_0 => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
    );',
                '<?php
    return array(
        self::STATUS_UNKNOWN_0 => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID_0    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
    );',
            ],
            [
                '<?php
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
    );',
                '<?php
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
    );',
            ],
            [
                '<?php
    Foo::test()->aaa(array(1 => 2))->bbb("a", "b");
',
            ],
            [
                '<?php
    function foo() {
        yield 1 => 2;
    }',
            ],
        ];
    }

    /**
     * @dataProvider provideAlignEqualsCases
     */
    public function testFixAlignEquals(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN]]);
        $this->doTest($expected, $input);
    }

    public function provideAlignEqualsCases(): array
    {
        return [
            [
                '<?php
    $a    = 1;
    $bbbb = \'
    $ddcccccc1 = 3;
    \';',
                '<?php
    $a = 1;
    $bbbb = \'
    $ddcccccc1 = 3;
    \';',
            ],
            [
                '<?php
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
    ',
                '<?php
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
    ',
            ],
            [
                '<?php
m(
    function ()
    {
        $d["a"]   = 1;
        $d["abc"] = 2;
    }
);
',
                '<?php
m(
    function ()
    {
        $d["a"] = 1;
        $d["abc"] = 2;
    }
);
',
            ],
            [
                '<?php

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
',
                '<?php

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
',
            ],
            [
                '<?php
fn ($x = 1) => $x + 3;
$f = 123;
',
            ],
            [
                '<?php
if (($c = count($array)) > 100) {
    $_data = \'100+\';
} elseif (($c = count($array)) > 0) {
    $_data = \'0+\';
}
',
            ],
            [
                '<?php
if (($c = count($array)) > 100) {
    $closure = fn ($x = 1) => $x + 3;
} elseif (($c = count($array)) > 0) {
    $closure = fn ($x = 1) => $x ** 3;
}
',
            ],
            [
                '<?php
$suppliersTitles          = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getTitle());
$suppliersClassifications = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getClassification());
',
                '<?php
$suppliersTitles = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getTitle());
$suppliersClassifications = $container->getContainerSuppliers()->map(fn (ContainerSupplier $containerSupplier) => $containerSupplier->getSupplier()->getClassification());
',
            ],
            [
                '<?php
$a              = [$s = 5, $d => 5, $c => 9,];
$ab             = [$bc = 1];
$someOtherArray = [$bcd = 1];
$a              = [$b];
$ab             = [$bc];
$abc            = [$bcd];
',
                '<?php
$a = [$s = 5, $d => 5, $c => 9,];
$ab = [$bc = 1];
$someOtherArray = [$bcd = 1];
$a = [$b];
$ab = [$bc];
$abc = [$bcd];
',
            ],
            [
                '<?php
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
',
            ],
            [
                '<?php
$obj = new class() extends SomeClass {
    public $someProperty = null;
};
',
            ],
            [
                '<?php
$fabricator->setOverrides(["first" => "Bobby"], $persist = false);
$bobbyUser = $fabricator->make();
$bobbyUser = $fabricator->make();
',
            ],
            [
                '<?php
$a = 1; if (true) {
$bbb = 1;
}
',
            ],
            [
                '<?php
$fabricator->setOverrides(
["first" => "Bobby"], $persist = false);
$fabricator->setOverrides(["first" => "Bobby"], $persist = false
);
',
            ],
            [
                '<?php
$start = (
    $input["start"] !== "" && ($date = DateTime::parse($input["start"]))
        ? $date->setTimezone("UTC")
        : $date->setTimezone("Europe/London")
);
',
            ],
        ];
    }

    /**
     * @dataProvider provideAlignDoubleArrowCases
     */
    public function testFixAlignDoubleArrow(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN]]);
        $this->doTest($expected, $input);
    }

    public function provideAlignDoubleArrowCases(): array
    {
        return [
            [
                '<?php
                switch ($a) {
                    case "prod":
                        break;
                }
                ',
            ],
            [
                '<?php
    $array = array(
        "closure" => function ($param1, $param2) {
            return;
        }
    );',
            ],
            [
                '<?php
    return new JsonResponse(array(
        "result" => "OK",
        "html"   => 1, /**/array(
            "foo"    => "bar",
            "foofoo" => array(
                "a"  => 1,
                "b"  => 2
            )
        ),)
    );',
                '<?php
    return new JsonResponse(array(
        "result" => "OK",
        "html" => 1, /**/array(
            "foo" => "bar",
            "foofoo" => array(
                "a" => 1,
                "b"  =>  2
            )
        ),)
    );',
            ],
            [
                '<?php
    return new JsonResponse([
        "result" => "OK",
        "html"   => renderView("views/my_view.html.twig", array(
            "foo"    => "bar",
            "foofoo" => 43,
        )),
    ]);',
                '<?php
    return new JsonResponse([
        "result" => "OK",
        "html" =>    renderView("views/my_view.html.twig", array(
            "foo" => "bar",
            "foofoo" => 43,
        )),
    ]);',
            ],
            [
                '<?php
    return new JsonResponse([
        "result" => "OK",
        "html"   => renderView("views/my_view.html.twig", [
            "foo"    => "bar",
            "foofoo" => 42,
        ]),
        "baz" => "OK",
    ]);',
                '<?php
    return new JsonResponse([
        "result" => "OK",
        "html" =>    renderView("views/my_view.html.twig", [
            "foo" =>   "bar",
            "foofoo" =>    42,
        ]),
        "baz" => "OK",
    ]);',
            ],
            [
                '<?php
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
    ];',
                '<?php
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
    ];',
            ],
            [
                '<?php
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
    }',
            ],
            [
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => [array("baz" => "Test")],
        "bar"  => array(),
    ];',
            ],
            [
                '<?php
    $data = array(
        "foo"  => "Bar",
        "main" => array("baz" => "Test"),
        "bar"  => array(),
    );',
            ],
            [
                '<?php
    $data = array(
        "foo"  => "Bar",
        "main" => array(array("baz" => "Test")),
        "bar"  => array(),
    );',
            ],
            [
                '<?php
    $var = [];
    foreach ($foo as $i => $bar) {
        $var[] = /* Comment */ [$i => $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach ($foo as $i => $bar) {
        $var[] = [$i => $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach ([1 => 2] as $k => $v) {
        $var[] = [$i => $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach (fncCall() as $k => $v){
        $var[] = [$i => $bar];
    }',
            ],
            [
                '<?php
    $var = [];
    foreach ($foo as $bar) {
        $var[] = [
            $i    => $bar,
            $iaaa => $bar,
        ];
    }',
            ],
            [
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => [["baz" => "Test", "bar" => "Test2"]],
        "bar"  => [],
    ];',
            ],
            [
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => ["baz" => "Test"],
        "bar"  => [],
    ];',
            ],
            [
                '<?php
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
    ];',
                '<?php
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
    ];',
            ],
            [
                '<?php
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
    );',
            ],
            [
                '<?php
    $arr = array(
        $a    => 1,
        $bbbb => \'
        $cccccccc2 = 3;
        \',
    );',
                '<?php
    $arr = array(
        $a => 1,
        $bbbb => \'
        $cccccccc2 = 3;
        \',
    );',
            ],
            [
                '<?php
    $arr = [
        $a    => 1,
        $bbbb => \'
        $cccccccc3 = 3;
        \',
    ];',
                '<?php
    $arr = [
        $a => 1,
        $bbbb => \'
        $cccccccc3 = 3;
        \',
    ];',
            ],
            [
                '<?php
    foreach($arr as $k => $v){
        $arr = array($k => 1,
            $a          => 1,
            $bbbb       => \'
            $cccccccc4 = 3;
            \',
        );
    }',
            ],
            [
                '<?php
    $a = array(
        10    => 11,
        20    => 22,
        30    => 33,
        40
            =>
                44,
    );',
                '<?php
    $a = array(
        10    =>    11,
        20  =>    22,
        30=>33,
        40
            =>
                44,
    );',
            ],
            [
                '<?php
    return array(
        " "    => "",    "\t"    => "",
        "\n"   => "", "\r"   => "",
        "\0"   => "", "\x0B"    => "",
    );',
                '<?php
    return array(
        " "   => "",    "\t"    => "",
        "\n"   => "", "\r"   => "",
        "\0"  => "", "\x0B"    => "",
    );',
            ],
            [
                '<?php
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
    );',
                '<?php
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
    );',
            ],
            [
                '<?php
    return array(
        self::STATUS_UNKNOWN_1    => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID_1    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
    );',
                '<?php
    return array(
        self::STATUS_UNKNOWN_1 => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID_1    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
    );',
            ],
            [
                '<?php
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
    );',
                '<?php
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
    );',
            ],
            [
                '<?php
    Foo::test()->aaa(array(1 => 2))->bbb("a", "b");
',
            ],
            [
                '<?php
    $inflect_male = array(
        "aitė\b" => "as",
        "ytė\b"  => "is",
        "iūtė\b" => "ius",
        "utė\b"  => "us",
    );',
                '<?php
    $inflect_male = array(
        "aitė\b" => "as",
        "ytė\b" => "is",
        "iūtė\b" => "ius",
        "utė\b" => "us",
    );',
            ],
            [
                '<?php
                $formMapper
                    ->add(\'foo\', null, [\'required\' => false])
                    ->add(\'dummy_field\', null, [\'required\' => false])
                ;
                ',
            ],
            [
                '<?php
                $formMapper
                    ->add(\'foo\', null, array(\'required\' => false))
                    ->add(\'dummy_field\', null, array(\'required\' => false))
                ;
                ',
            ],
            [
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(["server1" => $object], ["addedAt" => "DESC"], 5);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(["server2" => $object], ["checkedAt" => "desc"], 50);
    ',
            ],
            [
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array("server1" => $object), array("addedAt" => "DESC"), 5);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array("server2" => $object), array("checkedAt" => "desc"), 50);
    ',
            ],
            [
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy($foo[123]);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy($foo[123]);
    ',
            ],
            [
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
    ',
            ],
            [
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy((1 + 2));
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy((1 + 2));
    ',
            ],
            [
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array(1, 2));
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array(1, 2));
    ',
            ],
            [
                '<?php

    function foo() {}

    $bar = 42;

    $foo = [
        "test123" => "foo",
        "foo"     => $bar[123],
        "a"       => foo(),
        "b"       => 1,
    ];
    ',
                '<?php

    function foo() {}

    $bar = 42;

    $foo = [
        "test123" => "foo",
        "foo" => $bar[123],
        "a" => foo(),
        "b" => 1,
    ];
    ',
            ],
            [
                '<?php
    return array(
        self::STATUS_UNKNOWN_2    => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID_2    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
    );',
                '<?php
    return array(
        self::STATUS_UNKNOWN_2 => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID_2    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
    );',
            ],
            [
                '<?php
    return array(
        self::STATUS_UNKNOWN_3    => array((1 + 11)=> "?", "description" => "unknown"),
        self::STATUS_INVALID_3    => array((2 + 3)=> "III", "description" => "invalid file syntax, file ignored"),
    );',
                '<?php
    return array(
        self::STATUS_UNKNOWN_3 => array((1+11)=> "?", "description" => "unknown"),
        self::STATUS_INVALID_3    => array((2+3)=> "III", "description" => "invalid file syntax, file ignored"),
    );',
            ],
            [
                '<?php
    return [
        self::STATUS_UNKNOWN_4    => ["symbol" => "?", "description" => "unknown"],
        self::STATUS_INVALID_4    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
    ];',
                '<?php
    return [
        self::STATUS_UNKNOWN_4 => ["symbol" => "?", "description" => "unknown"],
        self::STATUS_INVALID_4    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
    ];',
            ],
            [
                '<?php
    return [
        self::STATUS_UNKNOWN_7    => [(1 + 11)=> "?", "description" => "unknown"],
        self::STATUS_INVALID_7    => [(2 + 3)=> "III", "description" => "invalid file syntax, file ignored"],
    ];',
                '<?php
    return [
        self::STATUS_UNKNOWN_7 => [(1+11)=> "?", "description" => "unknown"],
        self::STATUS_INVALID_7    => [(2+3)=> "III", "description" => "invalid file syntax, file ignored"],
    ];',
            ],
            [
                '<?php
$b = [1 => function() {
    foreach([$a => 2] as $b) {
        $bv = [
            $b  => 2,
            $cc => 3,
        ];
    }}, 2 => 3];
',
                '<?php
$b = [1 => function() {
    foreach([$a => 2] as $b) {
        $bv = [
            $b => 2,
            $cc => 3,
        ];
    }}, 2 => 3];
',
            ],
            [
                '<?php
function asd() {
      return [
          "this"    => fn () => false,
          "is"      => fn () => false,
          "an"      => fn () => false,
          "example" => fn () => false,
          "array"   => fn () => false,
      ];
}
',
                '<?php
function asd() {
      return [
          "this" => fn () => false,
          "is" => fn () => false,
          "an" => fn () => false,
          "example" => fn () => false,
          "array" => fn () => false,
      ];
}
',
            ],
            [
                '<?php
collect()
    ->map(fn ($arg) => [])
    ->keyBy(fn ($arg) => []);
',
            ],
            [
                '<?php
if ($this->save([
    "bar"       => "baz",
    "barbarbar" => "baz",
])) {
    // Do the work
}
',
                '<?php
if ($this->save([
    "bar" => "baz",
    "barbarbar" => "baz",
])) {
    // Do the work
}
',
            ],
            [
                '<?php
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
',
            ],
            [
                '<?php
$array = [
    "foo"     => 123,
    "longkey" => "test",
    "baz"     => fn () => "value",
];
',
                '<?php
$array = [
    "foo" => 123,
    "longkey" => "test",
    "baz" => fn () => "value",
];
',
            ],
            [
                '<?php
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
',
                '<?php
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
',
            ],
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
            '<?php
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
            '
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

    public function provideFixPhp74Cases(): array
    {
        return [
            [
                '<?php
                    $a = fn() => null;
                    $b = fn() => null;
                ',
                '<?php
                    $a = fn()    =>      null;
                    $b = fn()      =>  null;
                ',
                ['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]],
            ],
            [
                '<?php $a ??= 1;',
                '<?php $a??=1;',
                ['operators' => ['??=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE]],
            ],
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testUnionTypesAreNotChanged(): void
    {
        $this->doTest(
            '<?php
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
            }'
        );
    }

    /**
     * @requires PHP 8.1
     */
    public function testIntersectionTypesAreNotChanged(): void
    {
        $this->doTest(
            '<?php
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
            }'
        );
    }
}
