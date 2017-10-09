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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer
 */
final class BinaryOperatorSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideWithTabsCases
     */
    public function testWithTabs($expected, $input = null, array $configuration = null)
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideWithTabsCases()
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
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideTestCases
     */
    public function testConfigured($expected, $input = null, array $configuration = null)
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideTestCases()
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
    $var = [];
    foreach ([
                1 => 2,
                2 => 3,
            ] as $k => $v) {
        $var[] = [$i => $bar];
    }',
                '<?php
    $var = [];
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
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFixDefaults($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideUnalignEqualsCases
     */
    public function testUnalignEquals($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideUnalignEqualsCases()
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
        $a[$b] = array();
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
        $a[$b] = array();
    }',
            ],
        ];
    }

    public function testWrongConfigItem()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '/^\[binary_operator_spaces\] Invalid configuration: The option "foo" does not exist\. Defined options are: "align_double_arrow", "align_equals", "default", "operators"\.$/'
        );

        $this->fixer->configure(['foo' => true]);
    }

    public function testWrongConfigOldValue()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '/^\[binary_operator_spaces\] Invalid configuration: The option "align_double_arrow" with value 123 is invalid\. Accepted values are: true, false, null\.$/'
        );

        $this->fixer->configure(['align_double_arrow' => 123]);
    }

    /**
     * @group legacy
     * @expectedDeprecation Given configuration is deprecated and will be removed in 3.0. Use configuration ['operators' => ['=' => 'align', '=>' => 'single_space']] as replacement for ['align_equals' => true, 'align_double_arrow' => false].
     */
    public function testWrongConfigOldDeprecated()
    {
        $this->fixer->configure([
            'align_equals' => true,
            'align_double_arrow' => false,
        ]);
    }

    /**
     * @group legacy
     * @expectedDeprecation Given configuration is deprecated and will be removed in 3.0. Use configuration ['operators' => ['=' => 'align']] as replacement for ['align_equals' => true, 'align_double_arrow' => null].
     */
    public function testWrongConfigOldDeprecated2()
    {
        $this->fixer->configure([
            'align_equals' => true,
            'align_double_arrow' => null,
        ]);
    }

    /**
     * @group legacy
     * @expectedDeprecation Given configuration is deprecated and will be removed in 3.0. Use configuration ['operators' => ['=>' => 'align']] as replacement for ['align_equals' => null, 'align_double_arrow' => true].
     */
    public function testWrongConfigOldDeprecated3()
    {
        $this->fixer->configure([
            'align_equals' => null,
            'align_double_arrow' => true,
        ]);
    }

    /**
     * @group legacy
     * @expectedDeprecation Given configuration is deprecated and will be removed in 3.0. Use configuration ['operators' => ['=' => 'single_space', '=>' => 'align']] as replacement for ['align_equals' => false, 'align_double_arrow' => true].
     */
    public function testWrongConfigOldDeprecated4()
    {
        $this->fixer->configure([
            'align_equals' => false,
            'align_double_arrow' => true,
        ]);
    }

    /**
     * @group legacy
     * @expectedDeprecation Given configuration is deprecated and will be removed in 3.0. Use configuration ['operators' => ['=' => 'align', '=>' => 'align']] as replacement for ['align_equals' => true, 'align_double_arrow' => true].
     */
    public function testWrongConfigOldDeprecated5()
    {
        $this->fixer->configure([
            'align_equals' => true,
            'align_double_arrow' => true,
        ]);

        // simple test to see if the old config is still used
        $this->doTest(
            '<?php
                $a = array(
                    1  => 2,
                    2  => 3,
                );

                $b   = 1;
                $c   =  2;
            ',
            '<?php
                $a = array(
                    1 => 2,
                    2  => 3,
                );

                $b = 1;
                $c   =  2;
            '
        );
    }

    public function testWrongConfigOldAndNewMixed()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '/^\[binary_operator_spaces\] Mixing old configuration with new configuration is not allowed\.$/'
        );

        $this->fixer->configure([
            'align_double_arrow' => true,
            'operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN],
        ]);
    }

    public function testWrongConfigTypeForOperators()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '/^\[binary_operator_spaces\] Invalid configuration: The option "operators" with value true is expected to be of type "array", but is of type "boolean"\.$/'
        );

        $this->fixer->configure(['operators' => true]);
    }

    public function testWrongConfigTypeForOperatorsKey()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '/^\[binary_operator_spaces\] Invalid configuration: Unexpected "operators" key, expected any of ".*", got "integer#123"\.$/'
        );

        $this->fixer->configure(['operators' => [123 => 1]]);
    }

    public function testWrongConfigTypeForOperatorsKeyValue()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '/^\[binary_operator_spaces\] Invalid configuration: Unexpected value for operator "\+", expected any of ".*", got "string#abc"\.$/'
        );

        $this->fixer->configure(['operators' => ['+' => 'abc']]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideUnalignDoubleArrowCases
     */
    public function testUnalignDoubleArrow($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideUnalignDoubleArrowCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideAlignEqualsCases
     */
    public function testFixAlignEquals($expected, $input = null)
    {
        $this->fixer->configure(['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN]]);
        $this->doTest($expected, $input);
    }

    public function provideAlignEqualsCases()
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

    while (false) {
        $aa    = 2;
        $a[$b] = array();
    }

    for ($i = 0; $i < 10; $i++) {
        $aa    = 2;
        $a[$b] = array();
    }',
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
        $a[$b] = array();
    }',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideAlignDoubleArrowCases
     */
    public function testFixAlignDoubleArrow($expected, $input = null)
    {
        $this->fixer->configure(['operators' => ['=>' => BinaryOperatorSpacesFixer::ALIGN]]);
        $this->doTest($expected, $input);
    }

    public function provideAlignDoubleArrowCases()
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
        ];
    }

    public function testDoNotTouchEqualsAndArrowByConfig()
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
     * @requires PHP 7.0
     */
    public function testPHP70Cases()
    {
        $this->fixer->configure(['operators' => ['=' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE, '??' => BinaryOperatorSpacesFixer::ALIGN_SINGLE_SPACE_MINIMAL]]);
        $this->doTest(
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
'
        );
    }

    /**
     * @requires PHP 7.1
     *
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider providePHP71Cases
     */
    public function testPHP71Cases($expected, $input = null, array $configuration = null)
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function providePHP71Cases()
    {
        return [
            'align array destruction' => [
                '<?php
                    $c = [$d] = $e[1];
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
        ];
    }
}
