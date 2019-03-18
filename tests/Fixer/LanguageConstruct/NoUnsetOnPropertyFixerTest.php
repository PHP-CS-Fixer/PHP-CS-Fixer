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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gert de Pagter <BackEndTea@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\NoUnsetOnPropertyFixer
 */
final class NoUnsetOnPropertyFixerTest extends AbstractFixerTestCase
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

    public function provideFixCases()
    {
        return [
            'It replaces an unset on a property with  = null' => [
                '<?php $foo->bar = null;',
                '<?php unset($foo->bar);',
            ],
            'It replaces an unset on a property with  = null II' => [
                '<?php $foo->bar = null ;',
                '<?php unset($foo->bar );',
            ],
            'It replaces an unset on a static property with  = null' => [
                '<?php TestClass::$bar = null;',
                '<?php unset(TestClass::$bar);',
            ],
            'It does not replace unset on a variable with  = null' => [
                '<?php $b->a; unset($foo);',
            ],
            'It replaces multiple unsets on variables with = null' => [
                '<?php $foo->bar = null; $bar->foo = null; $bar->baz = null; $a->ba = null;',
                '<?php unset($foo->bar, $bar->foo, $bar->baz, $a->ba);',
            ],
            'It replaces multiple unsets, but not those that arent properties' => [
                '<?php $foo->bar = null; $bar->foo = null; unset($bar);',
                '<?php unset($foo->bar, $bar->foo, $bar);',
            ],
            'It replaces multiple unsets, but not those that arent properties in multiple places' => [
                '<?php unset($foo); $bar->foo = null; unset($bar);',
                '<?php unset($foo, $bar->foo, $bar);',
            ],
            'It replaces $this -> and self:: replacements' => [
                '<?php $this->bar = null; self::$foo = null; unset($bar);',
                '<?php unset($this->bar, self::$foo, $bar);',
            ],
            'It does not replace unsets on arrays' => [
                '<?php unset($bar->foo[0]);',
            ],
            'It works in a more complex unset' => [
                '<?php unset($bar->foo[0]); self::$foo = null; \Test\Baz::$fooBar = null; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b);',
                '<?php unset($bar->foo[0], self::$foo, \Test\Baz::$fooBar, $bar->foo[0], $this->foo, $a, $b);',
            ],
            'It works with consecutive unsets' => [
                '<?php $foo->bar = null; unset($foo); unset($bar); unset($baz); $this->ab = null;',
                '<?php unset($foo->bar, $foo, $bar, $baz, $this->ab);',
            ],
            'It does not replace unsets on arrays with special notation' => [
                '<?php unset($bar->foo{0});',
            ],
            'It works when around messy whitespace' => [
                '<?php
     unset($a); $this->b = null;
     $this->a = null; unset($b);
',
                '<?php
     unset($a, $this->b);
     unset($this->a, $b);
',
            ],
            'It works with weirdly placed comments' => [
                '<?php unset/*foo*/(/*bar*/$bar->foo[0]); self::$foo = null/*baz*/; /*ello*/\Test\Baz::$fooBar = null/*comment*/; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b);
                unset/*foo*/(/*bar*/$bar);',
                '<?php unset/*foo*/(/*bar*/$bar->foo[0], self::$foo/*baz*/, /*ello*/\Test\Baz::$fooBar/*comment*/, $bar->foo[0], $this->foo, $a, $b);
                unset/*foo*/(/*bar*/$bar);',
            ],
            'It does not mess with consecutive unsets' => [
                '<?php unset($a, $b, $c);
                $this->a = null;',
                '<?php unset($a, $b, $c);
                unset($this->a);',
            ],
            'It does not replace function call with class constant inside' => [
                '<?php unset($foos[array_search(BadFoo::NAME, $foos)]);',
            ],
            'It does not replace function call with class constant and property inside' => [
                '<?php unset($this->property[array_search(\Types::TYPE_RANDOM, $this->property)]);',
            ],
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
        return [
            'It does not break complex expressions' => [
                '<?php
                    unset(a()[b()["a"]]);
                    unset(a()[b()]);
                    unset(a()["a"]);
                    unset(a(){"a"});
                    unset(c($a)->a);
                ',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.3
     * @dataProvider provideFix73Cases
     */
    public function testFix73($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix73Cases()
    {
        return [
            'It replaces an unset on a property with  = null' => [
                '<?php $foo->bar = null;',
                '<?php unset($foo->bar,);',
            ],
            'It replaces multiple unsets, but not those that arent properties' => [
                '<?php $foo->bar = null; $bar->foo = null; unset($bar,);',
                '<?php unset($foo->bar, $bar->foo, $bar,);',
            ],
            'It replaces an unset on a static property with = null' => [
                '<?php TestClass::$bar = null;',
                '<?php unset(TestClass::$bar,);',
            ],
            'It does not replace unset on a variable with = null' => [
                '<?php $b->a; unset($foo,);',
            ],
            'It replaces multiple unsets on variables with = null' => [
                '<?php $foo->bar = null; $bar->foo = null; $bar->baz = null; $a->ba = null;',
                '<?php unset($foo->bar, $bar->foo, $bar->baz, $a->ba,);',
            ],
            'It replaces multiple unsets, but not those that arent properties in multiple places' => [
                '<?php unset($foo); $bar->foo = null; unset($bar,);',
                '<?php unset($foo, $bar->foo, $bar,);',
            ],
            'It replaces $this -> and self:: replacements' => [
                '<?php $this->bar = null; self::$foo = null; unset($bar,);',
                '<?php unset($this->bar, self::$foo, $bar,);',
            ],
            'It does not replace unsets on arrays' => [
                '<?php unset($bar->foo[0],);',
            ],
            'It works in a more complex unset' => [
                '<?php unset($bar->foo[0]); self::$foo = null; \Test\Baz::$fooBar = null; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b,);',
                '<?php unset($bar->foo[0], self::$foo, \Test\Baz::$fooBar, $bar->foo[0], $this->foo, $a, $b,);',
            ],
            'It works with consecutive unsets' => [
                '<?php $foo->bar = null; unset($foo); unset($bar); unset($baz); $this->ab = null;',
                '<?php unset($foo->bar, $foo, $bar, $baz, $this->ab,);',
            ],
            'It does not replace unsets on arrays with special notation' => [
                '<?php unset($bar->foo{0},);',
            ],
            'It works when around messy whitespace' => [
                '<?php
     unset($a); $this->b = null;
     $this->a = null; unset($b,);
',
                '<?php
     unset($a, $this->b,);
     unset($this->a, $b,);
',
            ],
            'It works with weirdly placed comments' => [
                '<?php unset/*foo*/(/*bar*/$bar->foo[0]); self::$foo = null/*baz*/; /*ello*/\Test\Baz::$fooBar = null/*comment*/; unset($bar->foo[0]); $this->foo = null; unset($a); unset($b,);
                unset/*foo*/(/*bar*/$bar,);',
                '<?php unset/*foo*/(/*bar*/$bar->foo[0], self::$foo/*baz*/, /*ello*/\Test\Baz::$fooBar/*comment*/, $bar->foo[0], $this->foo, $a, $b,);
                unset/*foo*/(/*bar*/$bar,);',
            ],
            'It does not mess with consecutive unsets' => [
                '<?php unset($a, $b, $c,);
                $this->a = null;',
                '<?php unset($a, $b, $c,);
                unset($this->a,);',
            ],
            'It does not replace function call with class constant inside' => [
                '<?php unset($foos[array_search(BadFoo::NAME, $foos)],);',
            ],
            'It does not replace function call with class constant and property inside' => [
                '<?php unset($this->property[array_search(\Types::TYPE_RANDOM, $this->property)],);',
            ],
            [
                '<?php $foo->bar = null ;',
                '<?php unset($foo->bar, );',
            ],
            [
                '<?php $foo->bar = null ;',
                '<?php unset($foo->bar ,);',
            ],
            [
                '<?php $foo->bar = null  ;',
                '<?php unset($foo->bar , );',
            ],
        ];
    }
}
