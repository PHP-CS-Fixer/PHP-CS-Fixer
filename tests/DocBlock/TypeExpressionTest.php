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
use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;

/**
 * @covers \PhpCsFixer\DocBlock\TypeExpression
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class TypeExpressionTest extends TestCase
{
    /**
     * @param null|list<string> $expectedTypes
     *
     * @dataProvider provideGetConstTypesCases
     * @dataProvider provideGetTypesCases
     */
    public function testGetTypes(string $typesExpression, ?array $expectedTypes = null): void
    {
        if (null === $expectedTypes) {
            $expectedTypes = [$typesExpression];
        }

        $expression = $this->parseTypeExpression($typesExpression, null, []);
        self::assertSame($expectedTypes, $expression->getTypes());

        $unionTestNs = '__UnionTest__';
        $unionExpression = $this->parseTypeExpression(
            $unionTestNs.'\A|'.$typesExpression.'|'.$unionTestNs.'\Z',
            null,
            []
        );
        if (!$expression->isCompositeType() || $expression->isUnionType()) {
            self::assertSame(
                [$unionTestNs.'\A', ...$expectedTypes, $unionTestNs.'\Z'],
                [...$unionExpression->getTypes()]
            );
        }
    }

    /**
     * @return iterable<int, array{string}>
     */
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
            yield [$type];
        }
    }

    /**
     * @return iterable<int, array{0: string, 1?: null|list<string>}>
     */
    public static function provideGetTypesCases(): iterable
    {
        yield ['int'];

        yield ['Foo5'];

        yield ['🚀_kůň'];

        yield ['positive-int'];

        yield ['?int'];

        yield ['? int'];

        yield ['int[]'];

        yield ['Foo[][]'];

        yield ['Foo [ ]  []'];

        yield ['int[]|null', ['int[]', 'null']];

        yield ['int[]|null|?int|array', ['int[]', 'null', '?int', 'array']];

        yield ['null|Foo\Bar|\Baz\Bax|int[]', ['null', 'Foo\Bar', '\Baz\Bax', 'int[]']];

        yield ['gen<int>'];

        yield ['int|gen<int>', ['int', 'gen<int>']];

        yield ['\int|\gen<\int, \bool>', ['\int', '\gen<\int, \bool>']];

        yield ['gen<int,  int>'];

        yield ['gen<int,  bool|string>'];

        yield ['gen<int,  string[]>'];

        yield ['gen<int,  gener<string, bool>>'];

        yield ['gen<int,  gener<string, null|bool>>'];

        yield ['gen<int>[][]'];

        yield ['non-empty-array<int>'];

        yield ['null|gen<int,  gener<string, bool>>|int|string[]', ['null', 'gen<int,  gener<string, bool>>', 'int', 'string[]']];

        yield ['null|gen<int,  gener<string, bool>>|int|array<int, string>|string[]', ['null', 'gen<int,  gener<string, bool>>', 'int', 'array<int, string>', 'string[]']];

        yield ['this'];

        yield ['@this'];

        yield ['$SELF|int', ['$SELF', 'int']];

        yield ['array<string|int, string>'];

        yield ['Collection<Foo<Bar>, Foo<Baz>>'];

        yield ['int | string', ['int', 'string']];

        yield ['Foo::*'];

        yield ['Foo::A'];

        yield ['Foo::A|Foo::B', ['Foo::A', 'Foo::B']];

        yield ['Foo::A*'];

        yield ['Foo::*0*_Bar'];

        yield ['?Foo::*[]'];

        yield ['array<Foo::A*>|null', ['array<Foo::A*>', 'null']];

        yield ['null|true|false|1|-1|1.5|-1.5|.5|1.|\'a\'|"b"', ['null', 'true', 'false', '1', '-1', '1.5', '-1.5', '.5', '1.', "'a'", '"b"']];

        yield ['int | "a" | A<B<C, D>, E<F::*|G[]>>', ['int', '"a"', 'A<B<C, D>, E<F::*|G[]>>']];

        yield ['class-string<Foo>'];

        yield ['A&B', ['A', 'B']];

        yield ['A & B', ['A', 'B']];

        yield ['array{}'];

        yield ['object{ }'];

        yield ['array{1: bool, 2: bool}'];

        yield ['array{a: int|string, b?: bool}'];

        yield ['array{\'a\': "a", "b"?: \'b\'}'];

        yield ['array { a : int | string , b ? : A<B, C> }'];

        yield ['array{bool, int}'];

        yield ['array{bool,}'];

        yield ['list{int, bool}'];

        yield ['object{ bool, foo2: int }'];

        yield ['ArRAY{ 1 }'];

        yield ['lIst{ 1 }'];

        yield ['OBJECT { x: 1 }'];

        yield ['array{a: int, b: int, with-dash: int}'];

        yield ['array{...}'];

        yield ['array{...<string>}'];

        yield ['array{bool, ...<int, string>}'];

        yield ['array{bool, ...}'];

        yield ['array{bool, ...<string>}'];

        yield ['array{a: bool,... }'];

        yield ['array{a: bool,...<string> }'];

        yield ["array{\n    a: Foo,\n    b: Bar\n}"];

        yield ["array{\n    Foo,\n    Bar,\n}"];

        yield ['list{int, ...<string>}'];

        yield ['callable'];

        yield ['callable(string)'];

        yield ['? callable(string): bool'];

        yield ['CAllable(string): bool'];

        yield ['callable(string,): bool'];

        yield ['callable(array<int, string>, array<int, Foo>): bool'];

        yield ['array<int, callable(string): bool>'];

        yield ['callable(string): callable(int)'];

        yield ['callable(string) : callable(int) : bool'];

        yield ['TheCollection<callable(Foo, Bar,Baz): Foo[]>|string[]|null', ['TheCollection<callable(Foo, Bar,Baz): Foo[]>', 'string[]', 'null']];

        yield ['Closure()'];

        yield ['Closure(string)'];

        yield ['\closure(string): void'];

        yield [\Closure::class];

        yield ['\Closure()'];

        yield ['\Closure(string)'];

        yield ['\Closure(string, bool)'];

        yield ['\Closure(string|int, bool)'];

        yield ['\Closure(string):bool'];

        yield ['\Closure(string): bool'];

        yield ['\Closure(string|int, bool): bool'];

        yield ['\Closure(float|int): (bool|int)'];

        yield ['Closure<T>(): T'];

        yield ['Closure<Tx, Ty>(): array{x: Tx, y: Ty}'];

        yield ['Closure<Tx, Ty>(): array{x: Tx, y: Ty, ...<Closure(): void>}'];

        yield ['array  <  int   , callable  (  string  )  :   bool  >'];

        yield ['Closure<T of Foo>(T): T'];

        yield ['Closure< T1 of Foo, T2 AS Foo >(T1): T2'];

        yield ['Closure<T = Foo>(T): T'];

        yield ['Closure<T1=int, T2 of Foo = Foo2>(T1): T2'];

        yield ['Closure<T of string = \'\'>(T): T'];

        yield ['Closure<Closure_can_be_regular_class>'];

        yield ['Closure(int $a)'];

        yield ['Closure(int $a): bool'];

        yield ['Closure(int $a, array<Closure(int ...$args): Item<X>>): bool'];

        yield ['Closure_can_be_aliased()'];

        yield ['Closure_can_be_aliased(): (u|v)'];

        yield ['(int)'];

        yield ['(int|\Exception)'];

        yield ['($foo is int ? false : true)'];

        yield ['($foo🚀3 is int ? false : true)'];

        yield ['\'a\\\'s"\\\\\n\r\t\'|"b\"s\'\\\\\n\r\t"', ['\'a\\\'s"\\\\\n\r\t\'', '"b\"s\'\\\\\n\r\t"']];

        yield ['string'.str_repeat('[]', 128)];

        yield [str_repeat('array<', 116).'string'.str_repeat('>', 116)];

        yield [self::makeLongArrayShapeType()];
    }

    /**
     * @dataProvider provideParseInvalidExceptionCases
     */
    public function testParseInvalidException(string $value): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to parse phpdoc type');
        new TypeExpression($value, null, []);
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideParseInvalidExceptionCases(): iterable
    {
        yield [''];

        yield ['0_class_cannot_start_with_number'];

        yield ['$0_variable_cannot_start_with_number'];

        yield ['class cannot contain space'];

        yield ['\\\class_with_double_backslash'];

        yield ['class\\\with_double_backslash'];

        yield ['class_with_end_backslash\\'];

        yield ['class/with_slash'];

        yield ['class--with_double_dash'];

        yield ['class.with_dot'];

        yield ['class,with_comma'];

        yield ['class@with_at_sign'];

        yield ['class:with_colon'];

        yield ['class#with_hash'];

        yield ['class//with_double_slash'];

        yield ['class$with_dollar'];

        yield ['class;with_semicolon'];

        yield ['class=with_equal_sign'];

        yield ['class+with_plus'];

        yield ['class?with_question_mark'];

        yield ['class*with_star'];

        yield ['class%with_percent'];

        yield ['(unclosed_parenthesis'];

        yield [')unclosed_parenthesis'];

        yield ['unclosed_parenthesis('];

        yield ['((unclosed_parenthesis)'];

        yield ['|vertical_bar_start'];

        yield ['&ampersand_start'];

        yield ['~tilde_start'];

        yield ['vertical_bar_end|'];

        yield ['ampersand_end&'];

        yield ['tilde_end~'];

        yield ['class||double_vertical_bar'];

        yield ['class&&double_ampersand'];

        yield ['class~~double_tilde'];

        yield ['array<>'];

        yield ['array<'];

        yield ['array<<'];

        yield ['array>'];

        yield ['array<<>'];

        yield ['array<>>'];

        yield ['array{'];

        yield ['array{ $this: 5 }'];

        yield ['array{...<>}'];

        yield ['array{bool, ...<>}'];

        yield ['array{bool, ...<int,>}'];

        yield ['array{bool, ...<,int>}'];

        yield ['array{bool, ...<int, int, int>}'];

        yield ['array{bool...<int>}'];

        yield ['array{,...<int>}'];

        yield ['array{...<int>,}'];

        yield ['g<,>'];

        yield ['g<,no_leading_comma>'];

        yield ['10__000'];

        yield ['[ array_syntax_is_invalid ]'];

        yield ['\' unclosed string'];

        yield ['\' unclosed string \\\''];

        yield 'generic with no arguments' => ['f<>'];

        yield 'generic Closure with no arguments' => ['Closure<>(): void'];

        yield 'generic Closure with non-identifier template argument' => ['Closure<A|B>(): void'];

        yield [substr(self::makeLongArrayShapeType(), 0, -1)];
    }

    public function testHugeType(): void
    {
        $nFlat = 2_000;
        $types = [];
        for ($i = 0; $i < $nFlat; ++$i) {
            $types[] = '\X\Foo'.$i;
        }
        $str = implode('|', $types);
        $expression = new TypeExpression($str, null, []);
        self::assertSame($types, $expression->getTypes());

        for ($i = 0; $i < 100; ++$i) {
            $str = 'array'.(1 === $i % 2 ? '{' : '<').$str.(1 === $i % 2 ? '}' : '>');
        }

        $typeLeft = '\Closure(A|B): void';
        $typeRight = '\Closure('.$typeLeft.'): void';
        $expression = new TypeExpression($typeLeft.'|('.$str.')|'.$typeRight, null, []);
        self::assertSame([$typeLeft, '('.$str.')', $typeRight], $expression->getTypes());
    }

    /**
     * @dataProvider provideGetTypesGlueCases
     */
    public function testGetTypesGlue(?string $expectedTypesGlue, string $typesExpression): void
    {
        $expression = new TypeExpression($typesExpression, null, []);
        self::assertSame($expectedTypesGlue, $expression->getTypesGlue());
    }

    /**
     * @return iterable<int, array{0: null|'&'|'|', 1: string}>
     */
    public static function provideGetTypesGlueCases(): iterable
    {
        yield [null, 'string'];

        yield ['|', 'bool|string'];

        yield ['&', 'Foo&Bar'];
    }

    /**
     * @dataProvider provideIsCompositeTypeCases
     */
    public function testIsCompositeType(bool $expectedIsCompositeType, string $typeExpression): void
    {
        $expression = new TypeExpression($typeExpression, null, []);

        self::assertSame($expectedIsCompositeType, $expression->isCompositeType());
    }

    /**
     * @return iterable<int, array{0: bool, 1: string}>
     */
    public static function provideIsCompositeTypeCases(): iterable
    {
        yield [false, 'string'];

        yield [false, 'iterable<Foo>'];

        yield [true, 'iterable&stringable'];

        yield [true, 'bool|string'];

        yield [true, 'Foo|(Bar&Baz)'];
    }

    /**
     * @dataProvider provideIsUnionTypeCases
     */
    public function testIsUnionType(bool $expectedIsUnionType, string $typeExpression): void
    {
        $expression = new TypeExpression($typeExpression, null, []);

        self::assertSame($expectedIsUnionType, $expression->isUnionType());
    }

    /**
     * @return iterable<int, array{0: bool, 1: string}>
     */
    public static function provideIsUnionTypeCases(): iterable
    {
        yield [false, 'string'];

        yield [false, 'iterable&stringable'];

        yield [true, 'bool|string'];

        yield [true, 'int|string|null'];

        yield [true, 'int|?string'];

        yield [true, 'int|null'];

        yield [false, '?int'];

        yield [true, 'Foo|Bar'];
    }

    /**
     * @dataProvider provideIsIntersectionTypeCases
     */
    public function testIsIntersectionType(bool $expectedIsIntersectionType, string $typeExpression): void
    {
        $expression = new TypeExpression($typeExpression, null, []);

        self::assertSame($expectedIsIntersectionType, $expression->isIntersectionType());
    }

    /**
     * @return iterable<int, array{0: bool, 1: string}>
     */
    public static function provideIsIntersectionTypeCases(): iterable
    {
        yield [false, 'string'];

        yield [false, 'string|int'];

        yield [true, 'Foo&Bar'];

        yield [true, 'Foo&Bar&?Baz'];

        yield [true, '\iterable&\Stringable'];
    }

    /**
     * @param list<NamespaceUseAnalysis> $namespaceUses
     *
     * @dataProvider provideGetCommonTypeCases
     */
    public function testGetCommonType(string $typesExpression, ?string $expectedCommonType, ?NamespaceAnalysis $namespace = null, array $namespaceUses = []): void
    {
        $expression = new TypeExpression($typesExpression, $namespace, $namespaceUses);
        self::assertSame($expectedCommonType, $expression->getCommonType());
    }

    /**
     * @return iterable<int, array{0: string, 1: null|string, 2?: null|NamespaceAnalysis, 3?: list<NamespaceUseAnalysis>}>
     */
    public static function provideGetCommonTypeCases(): iterable
    {
        $globalNamespace = new NamespaceAnalysis('', '', 0, 999, 0, 999);
        $appNamespace = new NamespaceAnalysis('App', 'App', 0, 999, 0, 999);

        $useTraversable = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, \Traversable::class, \Traversable::class, false, false, 0, 0);
        $useObjectAsTraversable = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, 'Foo', \Traversable::class, false, false, 0, 0);

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

        yield ['bool [ ]', 'array'];

        yield ['bool [ ][ ]', 'array'];

        yield ['array|iterable', 'iterable'];

        yield ['iterable|array', 'iterable'];

        yield ['array|Traversable', 'iterable'];

        yield ['array|\Traversable', 'iterable'];

        yield ['array|Traversable', 'iterable', $globalNamespace];

        yield ['iterable|Traversable', 'iterable'];

        yield ['array<string>', 'array'];

        yield ['array<int, string>', 'array'];

        yield ['array < string >', 'array'];

        yield ['list<int>', 'array'];

        yield ['iterable<string>', 'iterable'];

        yield ['iterable<int, string>', 'iterable'];

        yield ['\Traversable<string>', '\Traversable'];

        yield ['Traversable<int, string>', 'Traversable'];

        yield ['Collection<string>', 'Collection'];

        yield ['Collection<int, string>', 'Collection'];

        yield ['array{string}', 'array'];

        yield ['array { 1: string, \Closure(): void }', 'array'];

        yield ['Closure(): void', \Closure::class];

        yield ['array<int, string>|iterable<int, string>', 'iterable'];

        yield ['int[]|string[]', 'array'];

        yield ['int|null', 'int'];

        yield ['null|int', 'int'];

        yield ['?int', 'int'];

        yield ['?array<Foo>', 'array'];

        yield ['?list<Foo>', 'array'];

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

    /**
     * @return iterable<int, array{string, bool}>
     */
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

        yield ['?int', true];

        yield ['?\Closure(): void', true];
    }

    public function testMapTypes(): void
    {
        $typeExpression = new TypeExpression('Foo|Bar|($v is \Closure(X, Y): Z ? U : (V&W))', null, []);

        $addLeadingSlash = static function (TypeExpression $type) {
            $value = $type->toString();
            if (!str_starts_with($value, '\\') && !str_starts_with($value, '(')) {
                return new TypeExpression('\\'.$value, null, []);
            }

            return $type;
        };

        $removeLeadingSlash = static function (TypeExpression $type) {
            $value = $type->toString();
            if (str_starts_with($value, '\\')) {
                return new TypeExpression(substr($value, 1), null, []);
            }

            return $type;
        };

        $callLog = [];
        $typeExpression->mapTypes(static function (TypeExpression $type) use (&$callLog) {
            $callLog[] = $type->toString();

            if ('Y' === $type->toString()) {
                return new TypeExpression('_y_', null, []);
            }

            return $type;
        });
        self::assertSame([
            'Foo',
            'Bar',
            '\Closure',
            'X',
            'Y',
            'Z',
            '\Closure(X, _y_): Z',
            'U',
            'V',
            'W',
            'V&W',
            '(V&W)',
            '($v is \Closure(X, _y_): Z ? U : (V&W))',
            'Foo|Bar|($v is \Closure(X, _y_): Z ? U : (V&W))',
        ], $callLog);

        $typeExpression = $typeExpression->mapTypes($addLeadingSlash);
        $this->checkInnerTypeExpressionsStartIndex($typeExpression);
        self::assertSame('\Foo|\Bar|($v is \Closure(\X, \Y): \Z ? \U : (\V&\W))', $typeExpression->toString());

        $typeExpression = $typeExpression->mapTypes($addLeadingSlash);
        $this->checkInnerTypeExpressionsStartIndex($typeExpression);
        self::assertSame('\Foo|\Bar|($v is \Closure(\X, \Y): \Z ? \U : (\V&\W))', $typeExpression->toString());

        $typeExpression = $typeExpression->mapTypes($removeLeadingSlash);
        $this->checkInnerTypeExpressionsStartIndex($typeExpression);
        self::assertSame('Foo|Bar|($v is Closure(X, Y): Z ? U : (V&W))', $typeExpression->toString());

        $typeExpression = $typeExpression->mapTypes($removeLeadingSlash);
        $this->checkInnerTypeExpressionsStartIndex($typeExpression);
        self::assertSame('Foo|Bar|($v is Closure(X, Y): Z ? U : (V&W))', $typeExpression->toString());

        $typeExpression = $typeExpression->mapTypes($addLeadingSlash);
        $this->checkInnerTypeExpressionsStartIndex($typeExpression);
        self::assertSame('\Foo|\Bar|($v is \Closure(\X, \Y): \Z ? \U : (\V&\W))', $typeExpression->toString());
    }

    public function testWalkTypes(): void
    {
        $typeExpression = new TypeExpression('Foo|Bar|($v is \Closure(X, Y): Z ? U : (V&W))', null, []);

        $callLog = [];
        $typeExpression->walkTypes(static function (TypeExpression $type) use (&$callLog): void {
            $callLog[] = $type->toString();
        });
        self::assertSame([
            'Foo',
            'Bar',
            '\Closure',
            'X',
            'Y',
            'Z',
            '\Closure(X, Y): Z',
            'U',
            'V',
            'W',
            'V&W',
            '(V&W)',
            '($v is \Closure(X, Y): Z ? U : (V&W))',
            'Foo|Bar|($v is \Closure(X, Y): Z ? U : (V&W))',
        ], $callLog);
    }

    /**
     * @dataProvider provideSortTypesCases
     */
    public function testSortTypes(string $typesExpression, string $expectResult): void
    {
        $sortCaseFx = static fn (TypeExpression $a, TypeExpression $b): int => strcasecmp($a->toString(), $b->toString());
        $sortCrc32Fx = static fn (TypeExpression $a, TypeExpression $b): int => crc32($a->toString()) <=> crc32($b->toString());

        $expression = $this->parseTypeExpression($typesExpression, null, []);

        $expression = $expression->sortTypes($sortCaseFx);
        $this->checkInnerTypeExpressionsStartIndex($expression);
        self::assertSame($expectResult, $expression->toString());

        $expression = $expression->sortTypes($sortCrc32Fx);
        $this->checkInnerTypeExpressionsStartIndex($expression);
        $expression = $expression->sortTypes($sortCaseFx);
        $this->checkInnerTypeExpressionsStartIndex($expression);
        self::assertSame($expectResult, $expression->toString());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
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

        yield 'multiple union' => [
            'C___|D____|B__|A',
            'A|B__|C___|D____',
        ];

        yield 'multiple intersect' => [
            'C___&D____&B__&A',
            'A&B__&C___&D____',
        ];

        yield 'simple in generic' => [
            'array<int|bool>',
            'array<bool|int>',
        ];

        yield 'generic with multiple types' => [
            'array<int|bool, string|float>',
            'array<bool|int, float|string>',
        ];

        yield 'generic with trailing comma' => [
            'array<int|bool,>',
            'array<bool|int,>',
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

        yield 'unsealed array shape' => [
            'array{bool, ...<B|A>}',
            'array{bool, ...<A|B>}',
        ];

        yield 'unsealed array shape with key and value type' => [
            'array{bool, ...<B|A, D&C>}',
            'array{bool, ...<A|B, C&D>}',
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

        yield 'callable with multiple named arguments' => [
            'callable(int|bool $b, null|array $a)',
            'callable(bool|int $b, array|null $a)',
        ];

        yield 'callable with complex arguments' => [
            'callable(B|A&, D|Closure(): void..., array{}$foo=, $this $foo=): array{}',
            'callable(A|B&, Closure(): void|D..., array{}$foo=, $this $foo=): array{}',
        ];

        yield 'callable with trailing comma' => [
            'Closure( Y|X , ): B|A',
            'A|Closure( X|Y , ): B',
        ];

        yield 'simple in Closure argument' => [
            'Closure(int|bool)',
            'Closure(bool|int)',
        ];

        yield 'Closure with multiple arguments' => [
            'Closure(int|bool, null|array)',
            'Closure(bool|int, array|null)',
        ];

        yield 'simple in Closure argument with trailing comma' => [
            'Closure(int|bool,)',
            'Closure(bool|int,)',
        ];

        yield 'simple in Closure argument multiple arguments with trailing comma' => [
            'Closure(int|bool, null|array,)',
            'Closure(bool|int, array|null,)',
        ];

        yield 'simple in Closure return type' => [
            'Closure(): (string|float)',
            'Closure(): (float|string)',
        ];

        yield 'Closure with union return type and within union itself' => [
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

        yield 'generic Closure' => [
            'Closure<B, A>(y|x, U<p|o>|B|A): (Y|B|X)',
            'Closure<B, A>(x|y, A|B|U<o|p>): (B|X|Y)',
        ];

        yield 'generic Closure with bound template' => [
            'Closure<B of J|I, C, A of V|U, D of object>(B|A): array{B, A, B, C, D}',
            'Closure<B of I|J, C, A of U|V, D of object>(A|B): array{B, A, B, C, D}',
        ];

        yield 'generic Closure with template with default' => [
            'Closure<T = B&A>(T): void',
            'Closure<T = A&B>(T): void',
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
            'Closure(Y|X): (string|float)',
            'Closure(X|Y): (float|string)',
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

        yield 'mixed 2x | and & glue' => [
            'Foo|Foo2|Baz&Bar',
            'Bar&Baz|Foo|Foo2',
        ];

        yield 'mixed | and 2x & glue' => [
            'Foo|Baz&Baz2&Bar',
            'Bar&Baz&Baz2|Foo',
        ];
    }

    private static function makeLongArrayShapeType(): string
    {
        return 'array{'.implode(
            ', ',
            array_map(
                static fn (int $k): string => \sprintf('key%sno%d: int', 0 === $k % 2 ? '-' : '_', $k),
                range(1, 1_000),
            ),
        ).'}';
    }

    /**
     * Return type is recursive.
     *
     * @return list<array{int, string}|list<mixed>>
     */
    private function checkInnerTypeExpressionsStartIndex(TypeExpression $typeExpression): array
    {
        $innerTypeExpressions = \Closure::bind(static fn () => $typeExpression->innerTypeExpressions, null, TypeExpression::class)();

        $res = [];
        foreach ($innerTypeExpressions as ['start_index' => $innerStartIndex, 'expression' => $innerExpression]) {
            $innerExpressionStr = $innerExpression->toString();
            self::assertSame(
                $innerExpressionStr,
                substr($typeExpression->toString(), $innerStartIndex, \strlen($innerExpressionStr))
            );

            $res[] = [$innerStartIndex, $innerExpressionStr];

            $res[] = $this->checkInnerTypeExpressionsStartIndex($innerExpression);
        }

        return $res;
    }

    /**
     * Should be removed once https://github.com/php/php-src/pull/11396 is merged.
     */
    private function clearPcreRegexCache(): void
    {
        // there is no explicit php function to clear PCRE regex cache, but based
        // on https://www.php.net/manual/en/intro.pcre.php there are 4096 cache slots
        // pruned in FIFO fashion, so to clear the cache, replace all existing
        // cache slots with dummy regexes
        for ($i = 0; $i < 4_096; ++$i) {
            Preg::match('/^'.$i.'/', '');
        }
    }

    /**
     * Parse type expression with and without PCRE JIT.
     *
     * @param list<NamespaceUseAnalysis> $namespaceUses
     */
    private function parseTypeExpression(string $value, ?NamespaceAnalysis $namespace, array $namespaceUses): TypeExpression
    {
        $pcreJitBackup = \ini_get('pcre.jit');

        $expression = null;
        $innerExpressionsDataWithoutJit = null;

        try {
            foreach ([false, true] as $pcreJit) {
                ini_set('pcre.jit', $pcreJit ? '1' : '0');
                $this->clearPcreRegexCache();

                $expression = new TypeExpression($value, null, []);
                $innerExpressionsData = $this->checkInnerTypeExpressionsStartIndex($expression);

                if (false === $pcreJit) {
                    $innerExpressionsDataWithoutJit = $innerExpressionsData;
                } else {
                    self::assertSame($innerExpressionsDataWithoutJit, $innerExpressionsData);
                }
            }
        } finally {
            ini_set('pcre.jit', $pcreJitBackup);
            $this->clearPcreRegexCache();
        }

        return $expression;
    }
}
