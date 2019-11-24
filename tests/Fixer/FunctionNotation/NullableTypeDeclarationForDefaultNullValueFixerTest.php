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
 * @author HypeMC
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer
 */
final class NullableTypeDeclarationForDefaultNullValueFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP 7.1
     *
     * @param string $expected
     *
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix($expected)
    {
        $this->doTest($expected);
    }

    public function provideDoNotFixCases()
    {
        yield ['<?php function foo($param = null) {}'];
        yield ['<?php function foo($param1 = null, $param2 = null) {}'];
        yield ['<?php function foo(&$param = null) {}'];
        yield ['<?php function foo(& $param = null) {}'];
        yield ['<?php function foo(/**int*/ $param = null) {}'];
        yield ['<?php function foo(/**int*/ &$param = null) {}'];
        yield ['<?php function foo(&/*comment*/$param = null) {}'];
        yield ['<?php $foo = function ($param = null) {};'];
        yield ['<?php $foo = function (&$param = null) {};'];

        yield ['<?php function foo(?string $param = null) {}'];
        yield ['<?php function foo(?string $param= null) {}'];
        yield ['<?php function foo(?string $param =null) {}'];
        yield ['<?php function foo(?string $param=null) {}'];
        yield ['<?php function foo(?string $param1 = null, ?string $param2 = null) {}'];
        yield ['<?php function foo(?string &$param = null) {}'];
        yield ['<?php function foo(?string & $param = null) {}'];
        yield ['<?php function foo(?string /*comment*/$param = null) {}'];
        yield ['<?php function foo(?string /*comment*/&$param = null) {}'];
        yield ['<?php function foo(?string &/*comment*/$param = null) {}'];
        yield ['<?php function foo(? string $param = null) {}'];
        yield ['<?php function foo(?/*comment*/string $param = null) {}'];
        yield ['<?php function foo(? /*comment*/ string $param = null) {}'];
        yield ['<?php $foo = function (?string $param = null) {};'];
        yield ['<?php $foo = function (?string &$param = null) {};'];

        yield ['<?php function foo(?Baz $param = null) {}'];
        yield ['<?php function foo(?\Baz $param = null) {}'];
        yield ['<?php function foo(?Bar\Baz $param = null) {}'];
        yield ['<?php function foo(?\Bar\Baz $param = null) {}'];
        yield ['<?php function foo(?Baz &$param = null) {}'];
        yield ['<?php function foo(?\Baz &$param = null) {}'];
        yield ['<?php function foo(?Bar\Baz &$param = null) {}'];
        yield ['<?php function foo(?\Bar\Baz &$param = null) {}'];
        yield ['<?php function foo(?Baz & $param = null) {}'];
        yield ['<?php function foo(?\Baz & $param = null) {}'];
        yield ['<?php function foo(?Bar\Baz & $param = null) {}'];
        yield ['<?php function foo(?\Bar\Baz & $param = null) {}'];
        yield ['<?php function foo(?array &$param = null) {}'];
        yield ['<?php function foo(?array & $param = null) {}'];
        yield ['<?php function foo(?callable &$param = null) {}'];
        yield ['<?php function foo(?callable & $param = null) {}'];
        yield ['<?php $foo = function (?Baz $param = null) {};'];
        yield ['<?php $foo = function (?Baz &$param = null) {};'];
        yield ['<?php $foo = function (?Baz & $param = null) {};'];
        yield ['<?php class Test { public function foo(?Bar\Baz $param = null) {} }'];
        yield ['<?php class Test { public function foo(?self $param = null) {} }'];

        yield ['<?php function foo(...$param) {}'];
        yield ['<?php function foo(array ...$param) {}'];
        yield ['<?php function foo(?array ...$param) {}'];
    }

    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider provideFixCases
     * @dataProvider provideNonInverseOnlyFixCases
     */
    public function testFix($input, $expected)
    {
        if (\PHP_VERSION_ID < 70100) {
            $this->doTest($input);
        } else {
            $this->doTest($expected, $input);
        }
    }

    /**
     * @requires PHP 7.1
     *
     * @param string $input
     * @param string $expected
     *
     * @dataProvider provideFixCases
     * @dataProvider provideInverseOnlyFixCases
     */
    public function testFixInverse($expected, $input)
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        yield [
            '<?php function foo(string $param = null) {}',
            '<?php function foo(?string $param = null) {}',
        ];
        yield [
            '<?php function foo(string $param= null) {}',
            '<?php function foo(?string $param= null) {}',
        ];
        yield [
            '<?php function foo(string $param =null) {}',
            '<?php function foo(?string $param =null) {}',
        ];
        yield [
            '<?php function foo(string $param=null) {}',
            '<?php function foo(?string $param=null) {}',
        ];
        yield [
            '<?php function foo(string $param1 = null, string $param2 = null) {}',
            '<?php function foo(?string $param1 = null, ?string $param2 = null) {}',
        ];
        yield [
            '<?php function foo(string &$param = null) {}',
            '<?php function foo(?string &$param = null) {}',
        ];
        yield [
            '<?php function foo(string & $param = null) {}',
            '<?php function foo(?string & $param = null) {}',
        ];
        yield [
            '<?php function foo(string /*comment*/$param = null) {}',
            '<?php function foo(?string /*comment*/$param = null) {}',
        ];
        yield [
            '<?php function foo(string /*comment*/&$param = null) {}',
            '<?php function foo(?string /*comment*/&$param = null) {}',
        ];
        yield [
            '<?php function foo(string &/*comment*/$param = null) {}',
            '<?php function foo(?string &/*comment*/$param = null) {}',
        ];
        yield [
            '<?php $foo = function (string $param = null) {};',
            '<?php $foo = function (?string $param = null) {};',
        ];
        yield [
            '<?php $foo = function (string &$param = null) {};',
            '<?php $foo = function (?string &$param = null) {};',
        ];

        yield [
            '<?php function foo(Baz $param = null) {}',
            '<?php function foo(?Baz $param = null) {}',
        ];
        yield [
            '<?php function foo(\Baz $param = null) {}',
            '<?php function foo(?\Baz $param = null) {}',
        ];
        yield [
            '<?php function foo(Bar\Baz $param = null) {}',
            '<?php function foo(?Bar\Baz $param = null) {}',
        ];
        yield [
            '<?php function foo(\Bar\Baz $param = null) {}',
            '<?php function foo(?\Bar\Baz $param = null) {}',
        ];
        yield [
            '<?php function foo(Baz &$param = null) {}',
            '<?php function foo(?Baz &$param = null) {}',
        ];
        yield [
            '<?php function foo(\Baz &$param = null) {}',
            '<?php function foo(?\Baz &$param = null) {}',
        ];
        yield [
            '<?php function foo(Bar\Baz &$param = null) {}',
            '<?php function foo(?Bar\Baz &$param = null) {}',
        ];
        yield [
            '<?php function foo(\Bar\Baz &$param = null) {}',
            '<?php function foo(?\Bar\Baz &$param = null) {}',
        ];
        yield [
            '<?php function foo(Baz & $param = null) {}',
            '<?php function foo(?Baz & $param = null) {}',
        ];
        yield [
            '<?php function foo(\Baz & $param = null) {}',
            '<?php function foo(?\Baz & $param = null) {}',
        ];
        yield [
            '<?php function foo(Bar\Baz & $param = null) {}',
            '<?php function foo(?Bar\Baz & $param = null) {}',
        ];
        yield [
            '<?php function foo(\Bar\Baz & $param = null) {}',
            '<?php function foo(?\Bar\Baz & $param = null) {}',
        ];
        yield [
            '<?php function foo(array &$param = null) {}',
            '<?php function foo(?array &$param = null) {}',
        ];
        yield [
            '<?php function foo(array & $param = null) {}',
            '<?php function foo(?array & $param = null) {}',
        ];
        yield [
            '<?php function foo(callable $param = null) {}',
            '<?php function foo(?callable $param = null) {}',
        ];
        yield [
            '<?php $foo = function (Baz $param = null) {};',
            '<?php $foo = function (?Baz $param = null) {};',
        ];
        yield [
            '<?php $foo = function (Baz &$param = null) {};',
            '<?php $foo = function (?Baz &$param = null) {};',
        ];
        yield [
            '<?php $foo = function (Baz & $param = null) {};',
            '<?php $foo = function (?Baz & $param = null) {};',
        ];
        yield [
            '<?php class Test { public function foo(Bar\Baz $param = null) {} }',
            '<?php class Test { public function foo(?Bar\Baz $param = null) {} }',
        ];
        yield [
            '<?php class Test { public function foo(self $param = null) {} }',
            '<?php class Test { public function foo(?self $param = null) {} }',
        ];

        yield [
            '<?php function foo(iterable $param = null) {}',
            '<?php function foo(?iterable $param = null) {}',
        ];
        yield [
            '<?php $foo = function(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) {};',
            '<?php $foo = function(?array $a = null,
                    ?array $b = null, ?array     $c = null, ?array
                    $d = null) {};',
        ];
        yield [
            '<?php function foo(string $param = NULL) {}',
            '<?php function foo(?string $param = NULL) {}',
        ];
    }

    public function provideNonInverseOnlyFixCases()
    {
        yield [
            '<?php function foo( string $param = null) {}',
            '<?php function foo( ?string $param = null) {}',
        ];
        yield [
            '<?php function foo(/*comment*/string $param = null) {}',
            '<?php function foo(/*comment*/?string $param = null) {}',
        ];
        yield [
            '<?php function foo( /*comment*/ string $param = null) {}',
            '<?php function foo( /*comment*/ ?string $param = null) {}',
        ];
    }

    public function provideInverseOnlyFixCases()
    {
        yield [
            '<?php function foo(string $param = null) {}',
            '<?php function foo(? string $param = null) {}',
        ];
        yield [
            '<?php function foo(/*comment*/string $param = null) {}',
            '<?php function foo(?/*comment*/string $param = null) {}',
        ];
        yield [
            '<?php function foo(/*comment*/ string $param = null) {}',
            '<?php function foo(? /*comment*/ string $param = null) {}',
        ];
    }

    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider provideFixPhp74Cases
     * @requires PHP 7.4
     */
    public function testFixPhp74($input, $expected)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider provideFixPhp74Cases
     * @requires PHP 7.4
     */
    public function testFixInversePhp74($expected, $input)
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);

        $this->doTest($expected, $input);
    }

    public function provideFixPhp74Cases()
    {
        yield [
            '<?php $foo = fn (string $param = null) => null;',
            '<?php $foo = fn (?string $param = null) => null;',
        ];
        yield [
            '<?php $foo = fn (string &$param = null) => null;',
            '<?php $foo = fn (?string &$param = null) => null;',
        ];
        yield [
            '<?php $foo = fn (Baz $param = null) => null;',
            '<?php $foo = fn (?Baz $param = null) => null;',
        ];
        yield [
            '<?php $foo = fn (Baz &$param = null) => null;',
            '<?php $foo = fn (?Baz &$param = null) => null;',
        ];
        yield [
            '<?php $foo = fn (Baz & $param = null) => null;',
            '<?php $foo = fn (?Baz & $param = null) => null;',
        ];
        yield [
            '<?php $foo = fn(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) => null;',
            '<?php $foo = fn(?array $a = null,
                    ?array $b = null, ?array     $c = null, ?array
                    $d = null) => null;',
        ];
    }
}
