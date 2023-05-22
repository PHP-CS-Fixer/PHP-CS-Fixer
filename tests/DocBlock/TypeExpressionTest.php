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

namespace PhpCsFixer\Tests\DocBlock;

use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;

/**
 * @covers \PhpCsFixer\DocBlock\TypeExpression
 *
 * @internal
 */
final class TypeExpressionTest extends TestCase
{
    /**
     * @param string[] $expectedTypes
     *
     * @dataProvider provideGetConstTypesCases
     * @dataProvider provideGetTypesCases
     */
    public function testGetTypes(string $typesExpression, array $expectedTypes): void
    {
        $expression = new TypeExpression($typesExpression, null, []);
        self::assertSame($expectedTypes, $expression->getTypes());

        $unionTestNs = '__UnionTest__';
        $unionExpression = new TypeExpression(
            $unionTestNs.'\\A|'.$typesExpression.'|'.$unionTestNs.'\\Z',
            null,
            []
        );
        self::assertSame(
            [$unionTestNs.'\\A', ...$expectedTypes, $unionTestNs.'\\Z'],
            [...$unionExpression->getTypes()]
        );
    }

    public static function provideGetTypesCases(): iterable
    {
        yield ['int', ['int']];

        yield ['Foo5', ['Foo5']];

        yield ['🚀_kůň', ['🚀_kůň']];

        yield ['positive-int', ['positive-int']];

        yield ['?int', ['?int']];

        yield ['? int', ['? int']];

        yield ['int[]', ['int[]']];

        yield ['Foo[][]', ['Foo[][]']];

        yield ['Foo [ ]  []', ['Foo [ ]  []']];

        yield ['int[]|null', ['int[]', 'null']];

        yield ['int[]|null|?int|array', ['int[]', 'null', '?int', 'array']];

        yield ['null|Foo\Bar|\Baz\Bax|int[]', ['null', 'Foo\Bar', '\Baz\Bax', 'int[]']];

        yield ['gen<int>', ['gen<int>']];

        yield ['int|gen<int>', ['int', 'gen<int>']];

        yield ['\int|\gen<\int, \bool>', ['\int', '\gen<\int, \bool>']];

        yield ['gen<int,  int>', ['gen<int,  int>']];

        yield ['gen<int,  bool|string>', ['gen<int,  bool|string>']];

        yield ['gen<int,  string[]>', ['gen<int,  string[]>']];

        yield ['gen<int,  gener<string, bool>>', ['gen<int,  gener<string, bool>>']];

        yield ['gen<int,  gener<string, null|bool>>', ['gen<int,  gener<string, null|bool>>']];

        yield ['gen<int>[][]', ['gen<int>[][]']];

        yield ['non-empty-array<int>', ['non-empty-array<int>']];

        yield ['null|gen<int,  gener<string, bool>>|int|string[]', ['null', 'gen<int,  gener<string, bool>>', 'int', 'string[]']];

        yield ['null|gen<int,  gener<string, bool>>|int|array<int, string>|string[]', ['null', 'gen<int,  gener<string, bool>>', 'int', 'array<int, string>', 'string[]']];

        yield ['this', ['this']];

        yield ['@this', ['@this']];

        yield ['$SELF|int', ['$SELF', 'int']];

        yield ['array<string|int, string>', ['array<string|int, string>']];

        yield ['Collection<Foo<Bar>, Foo<Baz>>', ['Collection<Foo<Bar>, Foo<Baz>>']];

        yield ['int | string', ['int', 'string']];

        yield ['Foo::*', ['Foo::*']];

        yield ['Foo::A', ['Foo::A']];

        yield ['Foo::A|Foo::B', ['Foo::A', 'Foo::B']];

        yield ['Foo::A*', ['Foo::A*']];

        yield ['Foo::*0*_Bar', ['Foo::*0*_Bar']];

        yield ['?Foo::*[]', ['?Foo::*[]']];

        yield ['array<Foo::A*>|null', ['array<Foo::A*>', 'null']];

        yield ['null|true|false|1|-1|1.5|-1.5|.5|1.|\'a\'|"b"', ['null', 'true', 'false', '1', '-1', '1.5', '-1.5', '.5', '1.', "'a'", '"b"']];

        yield ['int | "a" | A<B<C, D>, E<F::*|G[]>>', ['int', '"a"', 'A<B<C, D>, E<F::*|G[]>>']];

        yield ['class-string<Foo>', ['class-string<Foo>']];

        yield ['A&B', ['A', 'B']];

        yield ['A & B', ['A', 'B']];

        yield ['array{}', ['array{}']];

        yield ['object{ }', ['object{ }']];

        yield ['array{1: bool, 2: bool}', ['array{1: bool, 2: bool}']];

        yield ['array{a: int|string, b?: bool}', ['array{a: int|string, b?: bool}']];

        yield ['array{\'a\': "a", "b"?: \'b\'}', ['array{\'a\': "a", "b"?: \'b\'}']];

        yield ['array { a : int | string , b ? : A<B, C> }', ['array { a : int | string , b ? : A<B, C> }']];

        yield ['array{bool, int}', ['array{bool, int}']];

        yield ['array{bool,}', ['array{bool,}']];

        yield ['list{int, bool}', ['list{int, bool}']];

        yield ['object{ bool, foo2: int }', ['object{ bool, foo2: int }']];

        yield ['callable(string)', ['callable(string)']];

        yield ['callable(string): bool', ['callable(string): bool']];

        yield ['callable(string,): bool', ['callable(string,): bool']];

        yield ['callable(array<int, string>, array<int, Foo>): bool', ['callable(array<int, string>, array<int, Foo>): bool']];

        yield ['array<int, callable(string): bool>', ['array<int, callable(string): bool>']];

        yield ['callable(string): callable(int)', ['callable(string): callable(int)']];

        yield ['callable(string) : callable(int) : bool', ['callable(string) : callable(int) : bool']];

        yield ['TheCollection<callable(Foo, Bar,Baz): Foo[]>|string[]|null', ['TheCollection<callable(Foo, Bar,Baz): Foo[]>', 'string[]', 'null']];

        yield ['Closure()', ['Closure()']];

        yield ['Closure(string)', ['Closure(string)']];

        yield ['\\Closure', ['\\Closure']];

        yield ['\\Closure()', ['\\Closure()']];

        yield ['\\Closure(string)', ['\\Closure(string)']];

        yield ['\\Closure(string, bool)', ['\\Closure(string, bool)']];

        yield ['\\Closure(string|int, bool)', ['\\Closure(string|int, bool)']];

        yield ['\\Closure(string):bool', ['\\Closure(string):bool']];

        yield ['\\Closure(string): bool', ['\\Closure(string): bool']];

        yield ['\\Closure(string|int, bool): bool', ['\\Closure(string|int, bool): bool']];

        yield ['\\Closure(float|int): (bool|int)', ['\\Closure(float|int): (bool|int)']];

        yield ['array  <  int   , callable  (  string  )  :   bool  >', ['array  <  int   , callable  (  string  )  :   bool  >']];

        yield ['(int)', ['(int)']];

        yield ['(int|\\Exception)', ['(int|\\Exception)']];

        yield ['($foo is int ? false : true)', ['($foo is int ? false : true)']];

        yield ['($foo🚀3 is int ? false : true)', ['($foo🚀3 is int ? false : true)']];

        yield ['\'a\\\'s"\\\\\n\r\t\'|"b\\"s\'\\\\\n\r\t"', ['\'a\\\'s"\\\\\n\r\t\'', '"b\\"s\'\\\\\n\r\t"']];
    }

    public static function provideGetConstTypesCases(): iterable
    {
        foreach ([
            'null',
            'true',
            'FALSE',

            '123',
            '+123',
            '-123',
            '0b0110101',
            '0o777',
            '0x7Fb4',
            '-0O777',
            '-0X7Fb4',
            '123_456',
            '0b01_01_01',
            '-0X7_Fb_4',
            '18_446_744_073_709_551_616', // 64-bit unsigned long + 1, larger than PHP_INT_MAX

            '123.4',
            '.123',
            '123.',
            '123e4',
            '123E4',
            '12.3e4',
            '+123.5',
            '-123.',
            '-123.4',
            '-.123',
            '-123.',
            '-123e-4',
            '-12.3e-4',
            '-1_2.3_4e5_6',
            '123E+80',
            '8.2023437675747321', // greater precision than 64-bit double
            '-0.0',

            '\'\'',
            '\'foo\'',
            '\'\\\\\'',
            '\'\\\'\'',
        ] as $type) {
            yield [$type, [$type]];
        }
    }

    /**
     * @dataProvider provideGetTypesGlueCases
     */
    public function testGetTypesGlue(string $expectedTypesGlue, string $typesExpression): void
    {
        $expression = new TypeExpression($typesExpression, null, []);
        self::assertSame($expectedTypesGlue, $expression->getTypesGlue());
    }

    public static function provideGetTypesGlueCases(): iterable
    {
        yield ['|', 'string']; // for backward behaviour

        yield ['|', 'bool|string'];

        yield ['&', 'Foo&Bar'];
    }

    /**
     * @param NamespaceUseAnalysis[] $namespaceUses
     *
     * @dataProvider provideCommonTypeCases
     */
    public function testGetCommonType(string $typesExpression, ?string $expectedCommonType, NamespaceAnalysis $namespace = null, array $namespaceUses = []): void
    {
        $expression = new TypeExpression($typesExpression, $namespace, $namespaceUses);
        self::assertSame($expectedCommonType, $expression->getCommonType());
    }

    public static function provideCommonTypeCases(): iterable
    {
        $globalNamespace = new NamespaceAnalysis('', '', 0, 999, 0, 999);
        $appNamespace = new NamespaceAnalysis('App', 'App', 0, 999, 0, 999);

        $useTraversable = new NamespaceUseAnalysis('Traversable', 'Traversable', false, 0, 0, NamespaceUseAnalysis::TYPE_CLASS);
        $useObjectAsTraversable = new NamespaceUseAnalysis('Foo', 'Traversable', false, 0, 0, NamespaceUseAnalysis::TYPE_CLASS);

        yield ['true', 'bool'];

        yield ['false', 'bool'];

        yield ['bool', 'bool'];

        yield ['int', 'int'];

        yield ['float', 'float'];

        yield ['string', 'string'];

        yield ['array', 'array'];

        yield ['object', 'object'];

        yield ['self', 'self'];

        yield ['static', 'static'];

        yield ['bool[]', 'array'];

        yield ['int[]', 'array'];

        yield ['float[]', 'array'];

        yield ['string[]', 'array'];

        yield ['array[]', 'array'];

        yield ['bool[][]', 'array'];

        yield ['int[][]', 'array'];

        yield ['float[][]', 'array'];

        yield ['string[][]', 'array'];

        yield ['array[][]', 'array'];

        yield ['array|iterable', 'iterable'];

        yield ['iterable|array', 'iterable'];

        yield ['array|Traversable', 'iterable'];

        yield ['array|\Traversable', 'iterable'];

        yield ['array|Traversable', 'iterable', $globalNamespace];

        yield ['iterable|Traversable', 'iterable'];

        yield ['array<string>', 'array'];

        yield ['array<int, string>', 'array'];

        yield ['iterable<string>', 'iterable'];

        yield ['iterable<int, string>', 'iterable'];

        yield ['\Traversable<string>', '\Traversable'];

        yield ['Traversable<int, string>', 'Traversable'];

        yield ['Collection<string>', 'Collection'];

        yield ['Collection<int, string>', 'Collection'];

        yield ['array<int, string>|iterable<int, string>', 'iterable'];

        yield ['int[]|string[]', 'array'];

        yield ['int|null', 'int'];

        yield ['null|int', 'int'];

        yield ['void', 'void'];

        yield ['never', 'never'];

        yield ['array|Traversable', 'iterable', null, [$useTraversable]];

        yield ['array|Traversable', 'iterable', $globalNamespace, [$useTraversable]];

        yield ['array|Traversable', 'iterable', $appNamespace, [$useTraversable]];

        yield ['self|static', 'self'];

        yield ['array|Traversable', null, null, [$useObjectAsTraversable]];

        yield ['array|Traversable', null, $globalNamespace, [$useObjectAsTraversable]];

        yield ['array|Traversable', null, $appNamespace, [$useObjectAsTraversable]];

        yield ['bool|int', null];

        yield ['string|bool', null];

        yield ['array<int, string>|Collection<int, string>', null];
    }

    /**
     * @dataProvider provideAllowsNullCases
     */
    public function testAllowsNull(string $typesExpression, bool $expectNullAllowed): void
    {
        $expression = new TypeExpression($typesExpression, null, []);
        self::assertSame($expectNullAllowed, $expression->allowsNull());
    }

    public static function provideAllowsNullCases(): iterable
    {
        yield ['null', true];

        yield ['mixed', true];

        yield ['null|mixed', true];

        yield ['int|bool|null', true];

        yield ['int|bool|mixed', true];

        yield ['int', false];

        yield ['bool', false];

        yield ['string', false];
    }

    /**
     * @dataProvider provideSortTypesCases
     */
    public function testSortTypes(string $typesExpression, string $expectResult): void
    {
        $expression = new TypeExpression($typesExpression, null, []);

        $expression->sortTypes(static function (TypeExpression $a, TypeExpression $b): int {
            return strcasecmp($a->toString(), $b->toString());
        });

        self::assertSame($expectResult, $expression->toString());
    }

    public static function provideSortTypesCases(): iterable
    {
        yield 'not a union type' => [
            'int',
            'int',
        ];

        yield 'simple' => [
            'int|bool',
            'bool|int',
        ];

        yield 'simple in generic' => [
            'array<int|bool>',
            'array<bool|int>',
        ];

        yield 'generic with multiple types' => [
            'array<int|bool, string|float>',
            'array<bool|int, float|string>',
        ];

        yield 'simple in array shape with int key' => [
            'array{0: int|bool}',
            'array{0: bool|int}',
        ];

        yield 'simple in array shape with string key' => [
            'array{"foo": int|bool}',
            'array{"foo": bool|int}',
        ];

        yield 'simple in array shape with multiple keys' => [
            'array{0: int|bool, "foo": int|bool}',
            'array{0: bool|int, "foo": bool|int}',
        ];

        yield 'simple in array shape with implicit key' => [
            'array{int|bool}',
            'array{bool|int}',
        ];

        yield 'simple in array shape with trailing comma' => [
            'array{int|bool,}',
            'array{bool|int,}',
        ];

        yield 'simple in array shape with multiple types with trailing comma' => [
            'array{int|bool, Foo|Bar, }',
            'array{bool|int, Bar|Foo, }',
        ];

        yield 'simple in array shape' => [
            'list{int, Foo|Bar}',
            'list{int, Bar|Foo}',
        ];

        yield 'array shape with multiple colons - array shape' => [
            'array{array{x:int|bool}, a:array{x:int|bool}}',
            'array{array{x:bool|int}, a:array{x:bool|int}}',
        ];

        yield 'array shape with multiple colons - callable' => [
            'array{array{x:int|bool}, int|bool, callable(): void}',
            'array{array{x:bool|int}, bool|int, callable(): void}',
        ];

        yield 'simple in callable argument' => [
            'callable(int|bool)',
            'callable(bool|int)',
        ];

        yield 'callable with multiple arguments' => [
            'callable(int|bool, null|array)',
            'callable(bool|int, array|null)',
        ];

        yield 'simple in callable return type' => [
            'callable(): (string|float)',
            'callable(): (float|string)',
        ];

        yield 'callable with union return type and within union itself' => [
            'callable(): (string|float)|bool',
            'bool|callable(): (float|string)',
        ];

        yield 'simple in closure argument' => [
            'Closure(int|bool)',
            'Closure(bool|int)',
        ];

        yield 'closure with multiple arguments' => [
            'Closure(int|bool, null|array)',
            'Closure(bool|int, array|null)',
        ];

        yield 'simple in closure argument with trailing comma' => [
            'Closure(int|bool,)',
            'Closure(bool|int,)',
        ];

        yield 'simple in closure argument multiple arguments with trailing comma' => [
            'Closure(int|bool, null|array,)',
            'Closure(bool|int, array|null,)',
        ];

        yield 'simple in closure return type' => [
            'Closure(): (string|float)',
            'Closure(): (float|string)',
        ];

        yield 'closure with union return type and within union itself' => [
            'Closure(): (string|float)|bool',
            'bool|Closure(): (float|string)',
        ];

        yield 'with multiple nesting levels' => [
            'array{0: Foo<int|bool>|Bar<callable(string|float|array<int|bool>): (Foo|Bar)>}',
            'array{0: Bar<callable(array<bool|int>|float|string): (Bar|Foo)>|Foo<bool|int>}',
        ];

        yield 'with multiple nesting levels and callable within union' => [
            'array{0: Foo<int|bool>|Bar<callable(string|float|array<int|bool>): (Foo|Bar)|Baz>}',
            'array{0: Bar<Baz|callable(array<bool|int>|float|string): (Bar|Foo)>|Foo<bool|int>}',
        ];

        yield 'complex type with Closure with $this' => [
            'array<string, string|array{ string|\Closure(mixed, string, $this): (int|float) }>|false',
            'array<string, array{ \Closure(mixed, string, $this): (float|int)|string }|string>|false',
        ];

        yield 'nullable generic' => [
            '?array<Foo|Bar>',
            '?array<Bar|Foo>',
        ];

        yield 'nullable callable' => [
            '?callable(Foo|Bar): (Foo|Bar)',
            '?callable(Bar|Foo): (Bar|Foo)',
        ];

        // This union type makes no sense in general (it should be `Bar|callable|null`)
        // but let's ensure nullable types are also sorted.
        yield 'nullable callable with union return type and within union itself' => [
            '?callable(Foo|Bar): (Foo|Bar)|?Bar',
            '?Bar|?callable(Bar|Foo): (Bar|Foo)',
        ];

        yield 'nullable array shape' => [
            '?array{0: Foo|Bar}',
            '?array{0: Bar|Foo}',
        ];

        yield 'simple types alternation' => [
            'array<Foo&Bar>',
            'array<Bar&Foo>',
        ];

        yield 'nesty stuff' => [
            'array<Level11&array<Level2|array<Level31&Level32>>>',
            'array<array<array<Level31&Level32>|Level2>&Level11>',
        ];

        yield 'parenthesized' => [
            '(Foo|Bar)',
            '(Bar|Foo)',
        ];

        yield 'parenthesized intersect' => [
            '(Foo&Bar)',
            '(Bar&Foo)',
        ];

        yield 'parenthesized in closure return type' => [
            'Closure(): (string|float)',
            'Closure(): (float|string)',
        ];

        yield 'conditional with variable' => [
            '($x is (CFoo|(CBaz&CBar)) ? (TFoo|(TBaz&TBar)) : (FFoo|(FBaz&FBar)))',
            '($x is ((CBar&CBaz)|CFoo) ? ((TBar&TBaz)|TFoo) : ((FBar&FBaz)|FFoo))',
        ];

        yield 'conditional with type' => [
            '((Foo|Bar) is x ? y : z)',
            '((Bar|Foo) is x ? y : z)',
        ];

        yield 'conditional in conditional' => [
            '((Foo|Bar) is x ? ($x is (CFoo|CBar) ? (TFoo|TBar) : (FFoo|FBar)) : z)',
            '((Bar|Foo) is x ? ($x is (CBar|CFoo) ? (TBar|TFoo) : (FBar|FFoo)) : z)',
        ];

        yield 'large numbers' => [
            '18_446_744_073_709_551_616|-8.2023437675747321e-18_446_744_073_709_551_616',
            '-8.2023437675747321e-18_446_744_073_709_551_616|18_446_744_073_709_551_616',
        ];
    }
}
