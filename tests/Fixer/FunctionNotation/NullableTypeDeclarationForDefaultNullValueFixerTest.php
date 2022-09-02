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
 * @author HypeMC
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

    public function provideDoNotFixCases(): iterable
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

    public function provideFixCases(): iterable
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

    public function provideInvertedFixCases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases($this->provideFixCases());
    }

    public function provideNonInverseOnlyFixCases(): iterable
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

    public function provideInverseOnlyFixCases(): iterable
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
     * @dataProvider provideInvertedFixPhp74Cases
     */
    public function testFixInversePhp74(string $expected, string $input): void
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);
        $this->doTest($expected, $input);
    }

    public function provideFixPhp74Cases(): iterable
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

    public function provideInvertedFixPhp74Cases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases($this->provideFixPhp74Cases());
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
     * @dataProvider provideInvertedFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixInverse80(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['use_nullable_type_declaration' => false]);
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): iterable
    {
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

        yield 'don\'t change union types' => [
            '<?php class Foo {
                public function __construct(private int | null $bar = null, $baz = 1) {}
                 public function aaa(int | string $bar = null, $baz = 1) {}
                 public function bbb(int | null $bar = null, $baz = 1) {}
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

    public function provideInvertedFix80Cases(): iterable
    {
        return TestCaseUtils::swapExpectedInputTestCases($this->provideFix80Cases());
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

    public function provideFixPre81Cases(): iterable
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

    public function provideFix81Cases(): iterable
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
}
