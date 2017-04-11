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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractAlignFixerHelper
 * @covers \PhpCsFixer\Fixer\Operator\AlignDoubleArrowFixerHelper
 * @covers \PhpCsFixer\Fixer\Operator\AlignEqualsFixerHelper
 * @covers \PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer
 */
final class BinaryOperatorSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
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
                '<?php $a = "c";',
                '<?php $a="c";',
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
                '<?php $d = $c + $a +     //
                $b;',
                '<?php $d =    $c+$a+     //
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
            '/^\[binary_operator_spaces\] Invalid configuration: The option "foo" does not exist\. Defined options are: "align_double_arrow", "align_equals"\.$/'
        );

        $this->fixer->configure(['foo' => true]);
    }

    public function testWrongConfigValue()
    {
        $this->setExpectedExceptionRegExp(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '/^\[binary_operator_spaces\] Invalid configuration: The option "align_double_arrow" with value 123 is invalid. Accepted values are: true, false, null\.$/'
        );

        $this->fixer->configure(['align_double_arrow' => 123]);
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
        $this->fixer->configure(['align_equals' => true]);
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
        $this->fixer->configure(['align_double_arrow' => true]);
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
        "html"   => 1, array(
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
        "html" => 1, array(
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
        $this->fixer->configure([
            'align_equals' => null,
            'align_double_arrow' => null,
        ]);

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
     * @requires PHP 7.1
     */
    public function testAlignArrayDestruction()
    {
        $this->fixer->configure(['align_equals' => true]);
        $this->doTest(
            '<?php
                $c = [$d] = $e[1];
                function A(){}[$a] = $a[$c];
                $b                 = 1;
            ',
            '<?php
                $c = [$d] = $e[1];
                function A(){}[$a] = $a[$c];
                $b = 1;
            '
        );
    }
}
