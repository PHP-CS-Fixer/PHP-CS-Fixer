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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\NoUnsetOnPropertyFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\LanguageConstruct\NoUnsetOnPropertyFixer>
 *
 * @author Gert de Pagter <BackEndTea@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoUnsetOnPropertyFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'It replaces an unset on a property with = null' => [
            '<?php $foo->bar = null;',
            '<?php unset($foo->bar);',
        ];

        yield 'It replaces an unset on a property with = null II' => [
            '<?php $foo->bar = null ;',
            '<?php unset($foo->bar );',
        ];

        yield 'It replaces an unset on a static property with = null' => [
            '<?php TestClass::$bar = null;',
            '<?php unset(TestClass::$bar);',
        ];

        yield 'It does not replace unset on a variable with = null' => [
            '<?php $b->a; unset($foo);',
        ];

        yield 'It replaces multiple unsets on variables with = null' => [
            '<?php $foo->bar = null; $bar->foo = null; $bar->baz = null; $a->ba = null;',
            '<?php unset($foo->bar, $bar->foo, $bar->baz, $a->ba);',
        ];

        yield 'It replaces multiple unsets, but not those that arent properties' => [
            '<?php $foo->bar = null; $bar->foo = null; unset($bar);',
            '<?php unset($foo->bar, $bar->foo, $bar);',
        ];

        yield 'It replaces multiple unsets, but not those that arent properties in multiple places' => [
            '<?php unset($foo); $bar->foo = null; unset($bar);',
            '<?php unset($foo, $bar->foo, $bar);',
        ];

        yield 'It replaces $this -> and self:: replacements' => [
            '<?php $this->bar = null; self::$foo = null; unset($bar);',
            '<?php unset($this->bar, self::$foo, $bar);',
        ];

        yield 'It does not replace unsets on arrays' => [
            '<?php unset($bar->foo[0]);',
        ];

        yield 'It works in a more complex unset' => [
            '<?php unset($bar->foo[0]); self::$foo = null; \Test\Baz::$fooBar = null; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b);',
            '<?php unset($bar->foo[0], self::$foo, \Test\Baz::$fooBar, $bar->foo[0], $this->foo, $a, $b);',
        ];

        yield 'It works with consecutive unsets' => [
            '<?php $foo->bar = null; unset($foo); unset($bar); unset($baz); $this->ab = null;',
            '<?php unset($foo->bar, $foo, $bar, $baz, $this->ab);',
        ];

        yield 'It works when around messy whitespace' => [
            '<?php
     unset($a); $this->b = null;
     $this->a = null; unset($b);
',
            '<?php
     unset($a, $this->b);
     unset($this->a, $b);
',
        ];

        yield 'It works with weirdly placed comments' => [
            '<?php unset/*foo*/(/*bar*/$bar->foo[0]); self::$foo = null/*baz*/; /*hello*/\Test\Baz::$fooBar = null/*comment*/; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b);
                unset/*foo*/(/*bar*/$bar);',
            '<?php unset/*foo*/(/*bar*/$bar->foo[0], self::$foo/*baz*/, /*hello*/\Test\Baz::$fooBar/*comment*/, $bar->foo[0], $this->foo, $a, $b);
                unset/*foo*/(/*bar*/$bar);',
        ];

        yield 'It does not mess with consecutive unsets' => [
            '<?php unset($a, $b, $c);
                $this->a = null;',
            '<?php unset($a, $b, $c);
                unset($this->a);',
        ];

        yield 'It does not replace function call with class constant inside' => [
            '<?php unset($foos[array_search(BadFoo::NAME, $foos)]);',
        ];

        yield 'It does not replace function call with class constant and property inside' => [
            '<?php unset($this->property[array_search(\Types::TYPE_RANDOM, $this->property)]);',
        ];

        yield 'It does not break complex expressions' => [
            '<?php
                unset(a()[b()["a"]]);
                unset(a()[b()]);
                unset(a()["a"]);
                unset(c($a)->a);
            ',
        ];

        yield 'It replaces an unset on a property with = null 1' => [
            '<?php $foo->bar = null;',
            '<?php unset($foo->bar,);',
        ];

        yield 'It replaces multiple unsets, but not those that arent properties 1' => [
            '<?php $foo->bar = null; $bar->foo = null; unset($bar,);',
            '<?php unset($foo->bar, $bar->foo, $bar,);',
        ];

        yield 'It replaces an unset on a static property with = null 1' => [
            '<?php TestClass::$bar = null;',
            '<?php unset(TestClass::$bar,);',
        ];

        yield 'It does not replace unset on a variable with = null 1' => [
            '<?php $b->a; unset($foo,);',
        ];

        yield 'It replaces multiple unsets on variables with = null 1' => [
            '<?php $foo->bar = null; $bar->foo = null; $bar->baz = null; $a->ba = null;',
            '<?php unset($foo->bar, $bar->foo, $bar->baz, $a->ba,);',
        ];

        yield 'It replaces multiple unsets, but not those that arent properties in multiple places 1' => [
            '<?php unset($foo); $bar->foo = null; unset($bar,);',
            '<?php unset($foo, $bar->foo, $bar,);',
        ];

        yield 'It replaces $this -> and self:: replacements 1' => [
            '<?php $this->bar = null; self::$foo = null; unset($bar,);',
            '<?php unset($this->bar, self::$foo, $bar,);',
        ];

        yield 'It does not replace unsets on arrays 1' => [
            '<?php unset($bar->foo[0],);',
        ];

        yield 'It works in a more complex unset 1' => [
            '<?php unset($bar->foo[0]); self::$foo = null; \Test\Baz::$fooBar = null; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b,);',
            '<?php unset($bar->foo[0], self::$foo, \Test\Baz::$fooBar, $bar->foo[0], $this->foo, $a, $b,);',
        ];

        yield 'It works with consecutive unsets 1' => [
            '<?php $foo->bar = null; unset($foo); unset($bar); unset($baz); $this->ab = null;',
            '<?php unset($foo->bar, $foo, $bar, $baz, $this->ab,);',
        ];

        yield 'It works when around messy whitespace 1' => [
            '<?php
     unset($a); $this->b = null;
     $this->a = null; unset($b,);
',
            '<?php
     unset($a, $this->b,);
     unset($this->a, $b,);
',
        ];

        yield 'It works with weirdly placed comments 11' => [
            '<?php unset/*foo*/(/*bar*/$bar->foo[0]); self::$foo = null/*baz*/; /*hello*/\Test\Baz::$fooBar = null/*comment*/; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b,);
                unset/*foo*/(/*bar*/$bar,);',
            '<?php unset/*foo*/(/*bar*/$bar->foo[0], self::$foo/*baz*/, /*hello*/\Test\Baz::$fooBar/*comment*/, $bar->foo[0], $this->foo, $a, $b,);
                unset/*foo*/(/*bar*/$bar,);',
        ];

        yield 'It does not mess with consecutive unsets 1' => [
            '<?php unset($a, $b, $c,);
                $this->a = null;',
            '<?php unset($a, $b, $c,);
                unset($this->a,);',
        ];

        yield 'It does not replace function call with class constant inside 1' => [
            '<?php unset($foos[array_search(BadFoo::NAME, $foos)],);',
        ];

        yield 'It does not replace function call with class constant and property inside 1' => [
            '<?php unset($this->property[array_search(\Types::TYPE_RANDOM, $this->property)],);',
        ];

        yield [
            '<?php $foo->bar = null ;',
            '<?php unset($foo->bar, );',
        ];

        yield [
            '<?php $foo->bar = null ;',
            '<?php unset($foo->bar ,);',
        ];

        yield [
            '<?php $foo->bar = null  ;',
            '<?php unset($foo->bar , );',
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
     * @return iterable<string, array{string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield 'It does not replace unsets on arrays with special notation' => [
            '<?php unset($bar->foo{0});',
        ];

        yield 'It does not replace unsets on arrays with special notation 1' => [
            '<?php unset($bar->foo{0},);',
        ];

        yield 'It does not break curly access expressions' => [
            '<?php unset(a(){"a"});',
        ];
    }
}
