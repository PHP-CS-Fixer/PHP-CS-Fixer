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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\RegularExpressionUtf8ModifierFixer
 */
final class RegularExpressionUtf8ModifierFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            'str_replace with search parameter looking like regular expression' => [
                '<?php str_replace("/\d/", "foo", "bar");',
            ],
            'preg_filter in wrong namespace.' => [
                '<?php Foo\preg_filter("/\d/", "bar", "baz");',
            ],
            'preg_filter class creating' => [
                '<?php new preg_filter("/\d/", "foo", "bar");',
            ],
            'preg_filter class static method call' => [
                '<?php Foo::preg_filter("/\d/", "bar", "baz");',
            ],
            'preg_filter class method call' => [
                '<?php $foo->preg_filter("/\d/", "bar", "baz");',
            ],
            'preg_filter different case' => [
                '<?php PREG_FILTER("/\d/u", "foo", "bar");',
                '<?php PREG_FILTER("/\d/", "foo", "bar");',
            ],
            'preg_filter with string' => [
                '<?php \preg_filter("/foo/u", "bar", "baz");',
                '<?php \preg_filter("/foo/", "bar", "baz");',
            ],
            'preg_filter with digit' => [
                '<?php preg_filter("/\d/u", "foo", "bar");',
                '<?php preg_filter("/\d/", "foo", "bar");',
            ],
            'preg_filter with delimiter inside of pattern' => [
                '<?php preg_filter("/foo\/bar/u", "baz", "qux");',
                '<?php preg_filter("/foo\/bar/", "baz", "qux");',
            ],
            'preg_filter with "u" modifier already used' => [
                '<?php preg_filter("/\d/u", "foo", "bar");',
            ],
            'preg_filter with other modifiers' => [
                '<?php preg_filter("/\d/imsu", "foo", "bar");',
                '<?php preg_filter("/\d/ims", "foo", "bar");',
            ],
            'preg_filter with "u" modifier already used among others' => [
                '<?php preg_filter("/\d/imus", "foo", "bar");',
            ],
            'preg_filter with hash as delimiter' => [
                '<?php preg_filter("#\d#u", "foo", "bar");',
                '<?php preg_filter("#\d#", "foo", "bar");',
            ],
            'preg_filter with array of single pattern' => [
                '<?php preg_filter(array("/foo/u"), "bar", "baz");',
                '<?php preg_filter(array("/foo/"), "bar", "baz");',
            ],
            'preg_filter with function return' => [
                '<?php preg_filter(foo(), "bar", "baz");',
            ],
            'preg_filter with function with parameter looking like regular expression' => [
                '<?php preg_filter(foo("/bar/"), "baz", "qux");',
            ],
            'preg_filter with long syntax array of patterns' => [
                '<?php preg_filter(array("/\d/u", "/[a-z]/u"), array("foo", "bar"), "baz");',
                '<?php preg_filter(array("/\d/", "/[a-z]/"), array("foo", "bar"), "baz");',
            ],
            'preg_filter with short syntax array of patterns' => [
                '<?php preg_filter(["/\d/u", "/[a-z]/u"], ["foo", "bar"], "baz");',
                '<?php preg_filter(["/\d/", "/[a-z]/"], ["foo", "bar"], "baz");',
            ],
            'preg_filter with long syntax associative array of patterns' => [
                '<?php preg_filter(array("foo" => "/\d/u", "bar" => "/[a-z]/u"), "baz", "qux");',
                '<?php preg_filter(array("foo" => "/\d/", "bar" => "/[a-z]/"), "baz", "qux");',
            ],
            'preg_filter with short syntax associative array of patterns' => [
                '<?php preg_filter(["foo" => "/\d/u", "bar" => "/[a-z]/u"], "baz", "qux");',
                '<?php preg_filter(["foo" => "/\d/", "bar" => "/[a-z]/"], "baz", "qux");',
            ],
            'preg_filter with array where keys look like regular expressions' => [
                '<?php preg_filter(["/\d/" => "/\d/u", "/\s/" => "/[a-z]/u"],  "foo", "bar");',
                '<?php preg_filter(["/\d/" => "/\d/", "/\s/" => "/[a-z]/"],  "foo", "bar");',
            ],
            'preg_filter with concatenation in the middle of pattern' => [
                '<?php preg_filter("/" . $foo . "/u", "bar", "baz");',
                '<?php preg_filter("/" . $foo . "/", "bar", "baz");',
            ],
            'preg_filter with concatenation with function in the middle of pattern' => [
                '<?php preg_filter("/". foo("x", "/\d/") . "/u", "bar", "baz");',
                '<?php preg_filter("/". foo("x", "/\d/") . "/", "bar", "baz");',
            ],
            'preg_filter with concatenation and unknown start' => [
                '<?php preg_filter($foo . "bar/u", "baz", "qux");',
                '<?php preg_filter($foo . "bar/", "baz", "qux");',
            ],
            'preg_filter with concatenation and unknown start and no delimiter' => [
                '<?php preg_filter($foo . "imsu", "bar", "baz");',
                '<?php preg_filter($foo . "ims", "bar", "baz");',
            ],
            'preg_filter with concatenation and unknown start and no delimiter but "u" modifier already used among others' => [
                '<?php preg_filter($foo . "imus", "bar", "baz");',
            ],
            'preg_filter with concatenation and unknown end' => [
                '<?php preg_filter("/". $foo, "bar", "baz");',
            ],
            'preg_filter with array and concatenation in the middle of pattern' => [
                '<?php preg_filter(["/" . $foo . "/u", "/" . bar("x", "/\d/") . "/u"], "baz", "qux");',
                '<?php preg_filter(["/" . $foo . "/", "/" . bar("x", "/\d/") . "/"], "baz", "qux");',
            ],
            'preg_filter with array and some unknown parts' => [
                '<?php preg_filter([$foo . "/u", "/" . $bar], "baz", "qux");',
                '<?php preg_filter([$foo . "/", "/" . $bar], "baz", "qux");',
            ],
            'preg_grep' => [
                '<?php preg_grep("/\d/u", ["foo", "bar"]);',
                '<?php preg_grep("/\d/", ["foo", "bar"]);',
            ],
            'preg_match_all' => [
                '<?php preg_match_all("/foo/u", "bar", $matches);',
                '<?php preg_match_all("/foo/", "bar", $matches);',
            ],
            'preg_match' => [
                '<?php preg_match("/foo/u", "bar", $matches);',
                '<?php preg_match("/foo/", "bar", $matches);',
            ],
            'preg_replace_callback_array' => [
                '<?php preg_replace_callback_array(
                        [
                            "/\d/u" => function ($match) {
                                return empty($match);
                            },
                            "/[a-z]/u" => function ($match) {
                                return empty($match);
                            }
                        ],
                        "foo"
                    );',
                '<?php preg_replace_callback_array(
                        [
                            "/\d/" => function ($match) {
                                return empty($match);
                            },
                            "/[a-z]/" => function ($match) {
                                return empty($match);
                            }
                        ],
                        "foo"
                    );',
            ],
            'preg_replace_callback_array with nested array with potential false positive key' => [
                '<?php preg_replace_callback_array(
                        [
                            "/\d/u" => function ($match) {
                                return ["/\d/" => "foo"];
                            },
                        ],
                        "bar"
                    );',
            ],
            'preg_replace_callback with single pattern' => [
                '<?php preg_replace_callback("/\d/u", function ($match) { return empty($match); }, "foo");',
                '<?php preg_replace_callback("/\d/", function ($match) { return empty($match); }, "foo");',
            ],
            'preg_replace_callback with array of patterns' => [
                '<?php preg_replace_callback(["/\d/u", "/[a-z]/u"], function ($match) { return empty($match); }, "foo");',
                '<?php preg_replace_callback(["/\d/", "/[a-z]/"], function ($match) { return empty($match); }, "foo");',
            ],
            'preg_replace with single pattern' => [
                '<?php preg_replace("/\d/u", "foo", "bar");',
                '<?php preg_replace("/\d/", "foo", "bar");',
            ],
            'preg_replace with array of patterns' => [
                '<?php preg_replace(["/\d/u", "/[a-z]/u"], ["foo", "bar"], "baz");',
                '<?php preg_replace(["/\d/", "/[a-z]/"], ["foo", "bar"], "baz");',
            ],
            'preg_split' => [
                '<?php preg_split("/\d/u", "foo", "bar");',
                '<?php preg_split("/\d/", "foo", "bar");',
            ],
        ];
    }
}
