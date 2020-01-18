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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\SimpleLambdaCallFixer
 */
final class SimpleLambdaCallFixerTest extends AbstractFixerTestCase
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
                $a["b"]{"c"}(1, 2);
            ',
            '<?php
                call_user_func($c, 1, 2);
                call_user_func($a["b"]["c"], 1, 2);
                call_user_func($a["b"]{"c"}, 1, 2);
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
        yield 'unsafe repeated variable' => [
            '<?php
                call_user_func($foo, $foo = \'bar\');',
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        yield 'call by variable' => [
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
    }
}
