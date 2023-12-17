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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\RegularCallableCallFixer
 */
final class RegularCallableCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'call by name - list' => [
            '<?php
                dont_touch_me(1, 2);
                foo();
                foo();
                call_user_func("foo" . "bar"); // not (yet?) supported by Fixer, possible since PHP 7+
                var_dump(1, 2);
                Bar\Baz::d(1, 2);
                \Bar\Baz::d(1, 2);',
            '<?php
                dont_touch_me(1, 2);
                call_user_func(\'foo\');
                call_user_func("foo");
                call_user_func("foo" . "bar"); // not (yet?) supported by Fixer, possible since PHP 7+
                call_user_func("var_dump", 1, 2);
                call_user_func("Bar\Baz::d", 1, 2);
                call_user_func("\Bar\Baz::d", 1, 2);',
        ];

        yield 'call by name - array' => [
            '<?php Bar\Baz::d(...[1, 2]);',
            '<?php call_user_func_array("Bar\Baz::d", [1, 2]);',
        ];

        yield 'call by array-as-name, not supported' => [
            '<?php
                call_user_func(array("Bar\baz", "myCallbackMethod"), 1, 2);
                call_user_func(["Bar\baz", "myCallbackMethod"], 1, 2);
                call_user_func([$obj, "myCallbackMethod"], 1, 2);
                call_user_func([$obj, $cb."Method"], 1, 2);
                call_user_func(array(__NAMESPACE__ ."Foo", "test"), 1, 2);
                call_user_func(array("Foo", "parent::method"), 1, 2); // no way to convert `parent::`
            ',
        ];

        yield 'call by variable' => [
            '<?php
                $c(1, 2);
                $a["b"]["c"](1, 2);
            ',
            '<?php
                call_user_func($c, 1, 2);
                call_user_func($a["b"]["c"], 1, 2);
            ',
        ];

        yield 'call with comments' => [
            '<?php
                dont_touch_me(/* a */1, 2/** b */);
                foo();
                foo(/* a */1, 2/** b */);
                foo(/* a *//** b *//** c */1/** d */,/** e */ 2);
                call_user_func("foo" . "bar"); // not (yet?) supported by Fixer, possible since PHP 7+
                var_dump(1, /*
                    aaa
                    */ 2);
                var_dump(3 /*
                    aaa
                    */, 4);
                Bar\Baz::d(1, 2);
                \Bar\Baz::d(1, 2);',
            '<?php
                dont_touch_me(/* a */1, 2/** b */);
                call_user_func(\'foo\');
                call_user_func("foo", /* a */1, 2/** b */);
                call_user_func("foo"/* a *//** b */, /** c */1/** d */,/** e */ 2);
                call_user_func("foo" . "bar"); // not (yet?) supported by Fixer, possible since PHP 7+
                call_user_func("var_dump", 1, /*
                    aaa
                    */ 2);
                call_user_func("var_dump", 3 /*
                    aaa
                    */, 4);
                call_user_func("Bar\Baz::d", 1, 2);
                call_user_func("\Bar\Baz::d", 1, 2);',
        ];

        yield 'single var' => [
            '<?php $foo() ?>',
            '<?php \call_user_func($foo) ?>',
        ];

        yield 'unsafe repeated variable' => [
            '<?php call_user_func($foo, $foo = "bar");',
        ];

        yield 'call by property' => [
            '<?php
                ($f->c)(1, 2);
                ($f->{c})(1, 2);
                ($x["y"]->c)(1, 2);
                ($x["y"]->{"c"})(1, 2);
            ',
            '<?php
                call_user_func($f->c, 1, 2);
                call_user_func($f->{c}, 1, 2);
                call_user_func($x["y"]->c, 1, 2);
                call_user_func($x["y"]->{"c"}, 1, 2);
            ',
        ];

        yield 'call by anon-function' => [
            '<?php
                (function ($a, $b) { var_dump($a, $b); })(1, 2);
                (static function ($a, $b) { var_dump($a, $b); })(1, 2);
            ',
            '<?php
                call_user_func(function ($a, $b) { var_dump($a, $b); }, 1, 2);
                call_user_func(static function ($a, $b) { var_dump($a, $b); }, 1, 2);
            ',
        ];

        yield 'complex cases' => [
            '<?php
                call_user_func(\'a\'.$a.$b, 1, 2);
                ($a/**/.$b)(1, 2);
                (function (){})();
                ($a["b"]["c"]->a)(1, 2, 3, 4);
                ($a::$b)(1, 2);
                ($a[1]::$b[2][3])([&$c], array(&$d));
            ',
            '<?php
                call_user_func(\'a\'.$a.$b, 1, 2);
                call_user_func($a/**/.$b, 1, 2);
                \call_user_func(function (){});
                call_user_func($a["b"]["c"]->a, 1, 2, 3, 4);
                call_user_func($a::$b, 1, 2);
                call_user_func($a[1]::$b[2][3], [&$c], array(&$d));
            ',
        ];

        yield [
            '<?php ($a(1, 2))([&$x], array(&$z));',
            '<?php call_user_func($a(1, 2), [&$x], array(&$z));',
        ];

        yield 'redeclare/override' => [
            '<?php
                if (!function_exists("call_user_func")) {
                    function call_user_func($foo){}
                }
            ',
        ];

        yield 'function name with escaped slash' => [
            '<?php \pack(...$args);',
            '<?php call_user_func_array("\\\\pack", $args);',
        ];

        yield 'function call_user_func_array with leading slash' => [
            '<?php \pack(...$args);',
            '<?php \call_user_func_array("\\\\pack", $args);',
        ];

        yield 'function call_user_func_array caps' => [
            '<?php \pack(...$args);',
            '<?php \CALL_USER_FUNC_ARRAY("\\\\pack", $args);',
        ];

        yield [
            '<?php foo(1,);',
            '<?php call_user_func("foo", 1,);',
        ];

        yield 'empty string double quote' => [
            '<?php call_user_func("", 1,);',
        ];

        yield 'empty string single quote' => [
            '<?php call_user_func(\'    \', 1,);',
        ];

        yield 'string with padding' => [
            '<?php call_user_func(" padded  ", 1,);',
        ];

        yield 'binary string lower double quote' => [
            '<?php call_user_func(b"foo", 1,);',
        ];

        yield 'binary string upper single quote' => [
            '<?php call_user_func(B"foo", 1,);',
        ];

        yield 'static property as first argument' => [
            '<?php
class Foo {
  public static $factory;
  public static function createFromFactory(...$args) {
    return call_user_func_array(static::$factory, $args);
  }
}',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield 'call by variable' => [
            '<?php
                $a{"b"}{"c"}(1, 2);
            ',
            '<?php
                call_user_func($a{"b"}{"c"}, 1, 2);
            ',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php \call_user_func(...) ?>',
        ];
    }
}
