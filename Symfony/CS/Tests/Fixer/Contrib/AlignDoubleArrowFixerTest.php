<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 *
 * @internal
 */
final class AlignDoubleArrowFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                switch ($a) {
                    case "prod":
                        break;
                }
                ',
            ),
            array(
                '<?php
    $array = array(
        "closure" => function ($param1, $param2) {
            return;
        }
    );',
            ),
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => [array("baz" => "Test")],
        "bar"  => array(),
    ];',
            ),
            array(
                '<?php
    $data = array(
        "foo"  => "Bar",
        "main" => array("baz" => "Test"),
        "bar"  => array(),
    );',
            ),
            array(
                '<?php
    $data = array(
        "foo"  => "Bar",
        "main" => array(array("baz" => "Test")),
        "bar"  => array(),
    );',
            ),
            array(
                '<?php
    $var = [];
    foreach ($foo as $i => $bar) {
        $var[] = /* Comment */ [$i => $bar];
    }',
            ),
            array(
                '<?php
    $var = [];
    foreach ($foo as $i => $bar) {
        $var[] = [$i => $bar];
    }',
            ),
            array(
                '<?php
    $var = [];
    foreach ([1 => 2] as $k => $v) {
        $var[] = [$i => $bar];
    }',
            ),
            array(
                '<?php
    $var = [];
    foreach (fncCall() as $k => $v){
        $var[] = [$i => $bar];
    }',
            ),
            array(
                '<?php
    $var = [];
    foreach ($foo as $bar) {
        $var[] = [
            $i    => $bar,
            $iaaa => $bar,
        ];
    }',
            ),
            array(
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => [["baz" => "Test", "bar" => "Test2"]],
        "bar"  => [],
    ];',
            ),
            array(
                '<?php
    $data = [
        "foo"  => "Bar",
        "main" => ["baz" => "Test"],
        "bar"  => [],
    ];',
            ),
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php
    $arr = array(
        $a    => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    );',
                '<?php
    $arr = array(
        $a => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    );',
            ),
            array(
                '<?php
    $arr = [
        $a    => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    ];',
                '<?php
    $arr = [
        $a => 1,
        $bbbb => \'
        $cccccccc = 3;
        \',
    ];',
            ),
            array(
                '<?php
    foreach($arr as $k => $v){
        $arr = array($k => 1,
            $a          => 1,
            $bbbb       => \'
            $cccccccc = 3;
            \',
        );
    }',
            ),
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php
    return array(
        self::STATUS_UNKNOWN    => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
    );',
                '<?php
    return array(
        self::STATUS_UNKNOWN => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID    => array("symbol" => "III", "description" => "invalid file syntax, file ignored"),
    );',
            ),
            array(
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
            ),
            array(
                '<?php
    Foo::test()->aaa(array(1 => 2))->bbb("a", "b");
',
            ),
            array(
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
            ),
            array(
                '<?php
                $formMapper
                    ->add(\'foo\', null, [\'required\' => false])
                    ->add(\'dummy_field\', null, [\'required\' => false])
                ;
                ',
            ),
            array(
                '<?php
                $formMapper
                    ->add(\'foo\', null, array(\'required\' => false))
                    ->add(\'dummy_field\', null, array(\'required\' => false))
                ;
                ',
            ),
            array(
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(["server1" => $object], ["addedAt" => "DESC"], 5);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(["server2" => $object], ["checkedAt" => "desc"], 50);
    ',
            ),
            array(
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array("server1" => $object), array("addedAt" => "DESC"), 5);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array("server2" => $object), array("checkedAt" => "desc"), 50);
    ',
            ),
            array(
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy($foo[123]);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy($foo[123]);
    ',
            ),
            array(
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy([1, 2, 3]);
    ',
            ),
            array(
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy((1 + 2));
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy((1 + 2));
    ',
            ),
            array(
                '<?php
    $dummy001 = $this->get("doctrine")->getRepository("AppBundle:Entity")->findBy(array(1, 2));
    $foobar = $this->getDoctrine()->getRepository("AppBundle:Entity")->findBy(array(1, 2));
    ',
            ),
            array(
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
            ),
            array(
                '<?php
    return array(
        self::STATUS_UNKNOWN    => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
    );',
                '<?php
    return array(
        self::STATUS_UNKNOWN => array("symbol" => "?", "description" => "unknown"),
        self::STATUS_INVALID    => array("symbol123" => "III", "description" => "invalid file syntax, file ignored"),
    );',
            ),
            array(
                '<?php
    return array(
        self::STATUS_UNKNOWN    => array((1+11)=> "?", "description" => "unknown"),
        self::STATUS_INVALID    => array((2+3)=> "III", "description" => "invalid file syntax, file ignored"),
    );',
                '<?php
    return array(
        self::STATUS_UNKNOWN => array((1+11)=> "?", "description" => "unknown"),
        self::STATUS_INVALID    => array((2+3)=> "III", "description" => "invalid file syntax, file ignored"),
    );',
            ),
            array(
                '<?php
    return [
        self::STATUS_UNKNOWN    => ["symbol" => "?", "description" => "unknown"],
        self::STATUS_INVALID    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
    ];',
                '<?php
    return [
        self::STATUS_UNKNOWN => ["symbol" => "?", "description" => "unknown"],
        self::STATUS_INVALID    => ["symbol123" => "III", "description" => "invalid file syntax, file ignored"],
    ];',
            ),
            array(
                '<?php
    return [
        self::STATUS_UNKNOWN    => [(1+11)=> "?", "description" => "unknown"],
        self::STATUS_INVALID    => [(2+3)=> "III", "description" => "invalid file syntax, file ignored"],
    ];',
                '<?php
    return [
        self::STATUS_UNKNOWN => [(1+11)=> "?", "description" => "unknown"],
        self::STATUS_INVALID    => [(2+3)=> "III", "description" => "invalid file syntax, file ignored"],
    ];',
            ),
        );
    }
}
