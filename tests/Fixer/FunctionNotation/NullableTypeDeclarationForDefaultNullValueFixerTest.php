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
use PhpCsFixer\Tests\Test\TestCaseUtils;

/**
 * @author HypeMC <hypemc@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer
 */
final class NullableTypeDeclarationForDefaultNullValueFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideDoNotFixCases(): iterable
    {
        yield ['<?php function foo($param = null) {}'];

        yield ['<?php function foo($param1 = null, $param2 = null) {}'];

        yield ['<?php function foo(&$param = null) {}'];

        yield ['<?php function foo(& $param = null) {}'];

        yield ['<?php function foo(/**int*/ $param = null) {}'];

        yield ['<?php function foo(/**int*/ &$param = null) {}'];

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

        yield ['<?php function foo(mixed $param = null) {}'];
    }

    /**
     * @dataProvider provideFixCases
     * @dataProvider provideNonInverseOnlyFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideInvertedFixCases
     * @dataProvider provideInverseOnlyFixCases
     */
    public function testFixInverse(string $expected, string $input): void
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php function foo(?string $param = null) {}',
            '<?php function foo(string $param = null) {}',
        ];

        yield [
            '<?php function foo(?string $param= null) {}',
            '<?php function foo(string $param= null) {}',
        ];

        yield [
            '<?php function foo(?string $param =null) {}',
            '<?php function foo(string $param =null) {}',
        ];

        yield [
            '<?php function foo(?string $param=null) {}',
            '<?php function foo(string $param=null) {}',
        ];

        yield [
            '<?php function foo(?string $param1 = null, ?string $param2 = null) {}',
            '<?php function foo(string $param1 = null, string $param2 = null) {}',
        ];

        yield [
            '<?php function foo(?string &$param = null) {}',
            '<?php function foo(string &$param = null) {}',
        ];

        yield [
            '<?php function foo(?string & $param = null) {}',
            '<?php function foo(string & $param = null) {}',
        ];

        yield [
            '<?php function foo(?string /*comment*/$param = null) {}',
            '<?php function foo(string /*comment*/$param = null) {}',
        ];

        yield [
            '<?php function foo(?string /*comment*/&$param = null) {}',
            '<?php function foo(string /*comment*/&$param = null) {}',
        ];

        yield [
            '<?php $foo = function (?string $param = null) {};',
            '<?php $foo = function (string $param = null) {};',
        ];

        yield [
            '<?php $foo = function (?string &$param = null) {};',
            '<?php $foo = function (string &$param = null) {};',
        ];

        yield [
            '<?php function foo(?Baz $param = null) {}',
            '<?php function foo(Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Baz $param = null) {}',
            '<?php function foo(\Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz $param = null) {}',
            '<?php function foo(Bar\Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Bar\Baz $param = null) {}',
            '<?php function foo(\Bar\Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?Baz &$param = null) {}',
            '<?php function foo(Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?\Baz &$param = null) {}',
            '<?php function foo(\Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz &$param = null) {}',
            '<?php function foo(Bar\Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?\Bar\Baz &$param = null) {}',
            '<?php function foo(\Bar\Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?Baz & $param = null) {}',
            '<?php function foo(Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Baz & $param = null) {}',
            '<?php function foo(\Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz & $param = null) {}',
            '<?php function foo(Bar\Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Bar\Baz & $param = null) {}',
            '<?php function foo(\Bar\Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?array &$param = null) {}',
            '<?php function foo(array &$param = null) {}',
        ];

        yield [
            '<?php function foo(?array & $param = null) {}',
            '<?php function foo(array & $param = null) {}',
        ];

        yield [
            '<?php function foo(?callable $param = null) {}',
            '<?php function foo(callable $param = null) {}',
        ];

        yield [
            '<?php $foo = function (?Baz $param = null) {};',
            '<?php $foo = function (Baz $param = null) {};',
        ];

        yield [
            '<?php $foo = function (?Baz &$param = null) {};',
            '<?php $foo = function (Baz &$param = null) {};',
        ];

        yield [
            '<?php $foo = function (?Baz & $param = null) {};',
            '<?php $foo = function (Baz & $param = null) {};',
        ];

        yield [
            '<?php class Test { public function foo(?Bar\Baz $param = null) {} }',
            '<?php class Test { public function foo(Bar\Baz $param = null) {} }',
        ];

        yield [
            '<?php class Test { public function foo(?self $param = null) {} }',
            '<?php class Test { public function foo(self $param = null) {} }',
        ];

        yield [
            '<?php function foo(?iterable $param = null) {}',
            '<?php function foo(iterable $param = null) {}',
        ];

        yield [
            '<?php $foo = function(?array $a = null,
                    ?array $b = null, ?array     $c = null, ?array
                    $d = null) {};',
            '<?php $foo = function(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) {};',
        ];

        yield [
            '<?php function foo(?string $param = NULL) {}',
            '<?php function foo(string $param = NULL) {}',
        ];
    }

    public static function provideInvertedFixCases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases(self::provideFixCases());
    }

    public static function provideNonInverseOnlyFixCases(): iterable
    {
        yield [
            '<?php function foo( ?string $param = null) {}',
            '<?php function foo( string $param = null) {}',
        ];

        yield [
            '<?php function foo(/*comment*/?string $param = null) {}',
            '<?php function foo(/*comment*/string $param = null) {}',
        ];

        yield [
            '<?php function foo( /*comment*/ ?string $param = null) {}',
            '<?php function foo( /*comment*/ string $param = null) {}',
        ];
    }

    public static function provideInverseOnlyFixCases(): iterable
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
     * @dataProvider provideFixPhp74Cases
     */
    public function testFixPhp74(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixInversePhp74Cases
     */
    public function testFixInversePhp74(string $expected, string $input): void
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFixPhp74Cases(): iterable
    {
        yield [
            '<?php $foo = fn (?string $param = null) => null;',
            '<?php $foo = fn (string $param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?string &$param = null) => null;',
            '<?php $foo = fn (string &$param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?Baz $param = null) => null;',
            '<?php $foo = fn (Baz $param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?Baz &$param = null) => null;',
            '<?php $foo = fn (Baz &$param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?Baz & $param = null) => null;',
            '<?php $foo = fn (Baz & $param = null) => null;',
        ];

        yield [
            '<?php $foo = fn(?array $a = null,
                    ?array $b = null, ?array     $c = null, ?array
                    $d = null) => null;',
            '<?php $foo = fn(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) => null;',
        ];
    }

    public static function provideFixInversePhp74Cases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases(self::provideFixPhp74Cases());
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixInverse80Cases
     * @dataProvider provideInverseOnlyFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixInverse80(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php function foo(string|int|null $param = null) {}',
            '<?php function foo(string|int $param = null) {}',
        ];

        yield [
            '<?php function foo(string|int|null $param = NULL) {}',
            '<?php function foo(string|int $param = NULL) {}',
        ];

        yield [
            '<?php function foo(string|int|null /*comment*/$param = null) {}',
            '<?php function foo(string|int /*comment*/$param = null) {}',
        ];

        yield [
            '<?php function foo(string | int|null &$param = null) {}',
            '<?php function foo(string | int &$param = null) {}',
        ];

        yield [
            '<?php function foo(string | int|null & $param = null) {}',
            '<?php function foo(string | int & $param = null) {}',
        ];

        yield [
            '<?php function foo(string | int|null /*comment*/&$param = null) {}',
            '<?php function foo(string | int /*comment*/&$param = null) {}',
        ];

        yield [
            '<?php function foo(string|int|null $param1 = null, string | int|null /*comment*/&$param2 = null) {}',
            '<?php function foo(string|int $param1 = null, string | int /*comment*/&$param2 = null) {}',
        ];

        yield 'trailing comma' => [
            '<?php function foo(?string $param = null,) {}',
            '<?php function foo(string $param = null,) {}',
        ];

        yield 'property promotion' => [
            '<?php class Foo {
                public function __construct(
                    public ?string $paramA = null,
                    protected ?string $paramB = null,
                    private ?string $paramC = null,
                    ?string $paramD = null,
                    $a = []
                ) {}
            }',
            '<?php class Foo {
                public function __construct(
                    public ?string $paramA = null,
                    protected ?string $paramB = null,
                    private ?string $paramC = null,
                    string $paramD = null,
                    $a = []
                ) {}
            }',
        ];

        yield 'attribute' => [
            '<?php function foo(#[AnAttribute] ?string $param = null) {}',
            '<?php function foo(#[AnAttribute] string $param = null) {}',
        ];

        yield 'attributes' => [
            '<?php function foo(
                #[AnAttribute] ?string $a = null,
                #[AnAttribute] ?string $b = null,
                #[AnAttribute] ?string $c = null
            ) {}',
            '<?php function foo(
                #[AnAttribute] string $a = null,
                #[AnAttribute] string $b = null,
                #[AnAttribute] string $c = null
            ) {}',
        ];
    }

    public static function provideFixInverse80Cases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases(self::provideFix80Cases());
    }

    public static function provideInverseOnlyFix80Cases(): iterable
    {
        yield [
            '<?php function foo(string $param = null) {}',
            '<?php function foo(string|null $param = null) {}',
        ];

        yield [
            '<?php function foo(string $param= null) {}',
            '<?php function foo(string | null $param= null) {}',
        ];

        yield [
            '<?php function foo(string $param =null) {}',
            '<?php function foo(string| null $param =null) {}',
        ];

        yield [
            '<?php function foo(string $param=null) {}',
            '<?php function foo(string |null $param=null) {}',
        ];

        yield [
            '<?php function foo(string $param1 = null, string $param2 = null) {}',
            '<?php function foo(null|string $param1 = null, null | string $param2 = null) {}',
        ];

        yield [
            '<?php function foo(string &$param = null) {}',
            '<?php function foo(null| string &$param = null) {}',
        ];

        yield [
            '<?php function foo(string & $param = null) {}',
            '<?php function foo(null |string & $param = null) {}',
        ];

        yield [
            '<?php function foo(string|int /*comment*/$param = null) {}',
            '<?php function foo(string|null|int /*comment*/$param = null) {}',
        ];

        yield [
            '<?php function foo(string | int /*comment*/&$param = null) {}',
            '<?php function foo(string | null | int /*comment*/&$param = null) {}',
        ];

        yield [
            '<?php $foo = function (string $param = null) {};',
            '<?php $foo = function (NULL | string $param = null) {};',
        ];

        yield [
            '<?php $foo = function (string|int &$param = null) {};',
            '<?php $foo = function (string|NULL|int &$param = null) {};',
        ];

        yield [
            '<?php function foo(Bar\Baz $param = null) {}',
            '<?php function foo(Bar\Baz|null $param = null) {}',
        ];

        yield [
            '<?php function foo(\Bar\Baz $param = null) {}',
            '<?php function foo(null | \Bar\Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(Bar\Baz &$param = null) {}',
            '<?php function foo(Bar\Baz | NULL &$param = null) {}',
        ];

        yield [
            '<?php function foo(\Bar\Baz &$param = null) {}',
            '<?php function foo(NULL|\Bar\Baz &$param = null) {}',
        ];

        yield [
            '<?php $foo = function(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) {};',
            '<?php $foo = function(array|null $a = null,
                    array | null $b = null, array | NULL     $c = null, NULL|array
                    $d = null) {};',
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @requires PHP <8.0
     *
     * @dataProvider provideFixPre81Cases
     */
    public function testFixPre81(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixPre81Cases(): iterable
    {
        yield 'do not fix pre PHP 8.1' => [
            '<?php
                function foo1(&/*comment*/$param = null) {}
                function foo2(?string &/*comment*/$param2 = null) {}
            ',
        ];

        $cases = [
            '<?php function foo(?string &/* comment */$param = null) {}',
            '<?php function foo(string &/* comment */$param = null) {}',
        ];

        yield [$cases[0], $cases[1]];

        yield [$cases[1], $cases[0], ['use_nullable_type_declaration' => false]];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
class Foo
{
    public function __construct(
        protected readonly ?bool $nullable = null,
    ) {}
}
',
        ];

        yield [
            '<?php

            class Foo {
                public function __construct(
                   public readonly ?string $readonlyString = null,
                   readonly public ?int $readonlyInt = null,
                ) {}
            }',
            ['use_nullable_type_declaration' => false],
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield 'Skip standalone null types' => [
            '<?php function foo(null $param = null) {}',
        ];

        yield 'Skip standalone NULL types' => [
            '<?php function foo(NULL $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|null $param = null) {}',
            '<?php function foo(\Bar\Baz&\Bar\Qux $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux|null $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|(\Bar\Quux&\Bar\Corge)|null $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|(\Bar\Quux&\Bar\Corge) $param = null) {}',
        ];

        yield [
            '<?php function foo(    (\Bar\Baz&\Bar\Qux)|null    $param = null) {}',
            '<?php function foo(    \Bar\Baz&\Bar\Qux    $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux|null &$param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux &$param = null) {}',
        ];

        yield [
            '<?php function foo(    (\Bar\Baz&\Bar\Qux)|null    &  $param = null) {}',
            '<?php function foo(    \Bar\Baz&\Bar\Qux    &  $param = null) {}',
        ];

        yield [
            '<?php function foo(    (\Bar\Baz&\Bar\Qux)|null/*comment*/&$param = null) {}',
            '<?php function foo(    \Bar\Baz&\Bar\Qux/*comment*/&$param = null) {}',
        ];
    }

    /**
     * @dataProvider provideFix82InverseCases
     *
     * @requires PHP 8.2
     */
    public function testFix82Inverse(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);
        $this->doTest($expected, $input);
    }

    public static function provideFix82InverseCases(): iterable
    {
        yield from TestCaseUtils::swapExpectedInputTestCases(self::provideFix82Cases());

        yield [
            '<?php function foo(\Bar\Baz&\Bar\Qux $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|NULL $param = null) {}',
        ];

        yield [
            '<?php function foo(\Bar\Baz&\Bar\Qux $param = null) {}',
            '<?php function foo(null|(\Bar\Baz&\Bar\Qux) $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|(\Bar\Quux&\Bar\Corge) $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|null|(\Bar\Quux&\Bar\Corge) $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux $param = null) {}',
            '<?php function foo(null|(\Bar\Baz&\Bar\Qux)|\Bar\Quux $param = null) {}',
        ];

        yield [
            '<?php function foo(    \Bar\Baz&\Bar\Qux     $param = null) {}',
            '<?php function foo(    (    \Bar\Baz&\Bar\Qux    )   |   null     $param = null) {}',
        ];

        yield [
            '<?php function foo(\Bar\Baz&\Bar\Qux    $param = null) {}',
            '<?php function foo(null    |    (    \Bar\Baz&\Bar\Qux    )    $param = null) {}',
        ];

        yield [
            '<?php function foo((    \Bar\Baz&\Bar\Qux    )|\Bar\Quux     $param = null) {}',
            '<?php function foo(null    |    (    \Bar\Baz&\Bar\Qux    )|\Bar\Quux     $param = null) {}',
        ];

        yield [
            '<?php function foo((    \Bar\Baz  &   \Bar\Qux    )|\Bar\Quux     & $param = null) {}',
            '<?php function foo(null    |    (    \Bar\Baz  &   \Bar\Qux    )|\Bar\Quux     & $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)    |  (\Bar\Quux&\Bar\Corge) $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)  |   null    |  (\Bar\Quux&\Bar\Corge) $param = null) {}',
        ];
    }
}
