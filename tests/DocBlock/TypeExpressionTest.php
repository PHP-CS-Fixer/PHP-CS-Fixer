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
     * @dataProvider provideGetTypesCases
     */
    public function testGetTypes(string $typesExpression, array $expectedTypes): void
    {
        $expression = new TypeExpression($typesExpression, null, []);
        static::assertSame($expectedTypes, $expression->getTypes());
    }

    public function provideGetTypesCases(): \Generator
    {
        yield ['int', ['int']];
        yield ['Foo[][]', ['Foo[][]']];
        yield ['int[]', ['int[]']];
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
        yield ['array<Foo::A*>|null', ['array<Foo::A*>', 'null']];
        yield ['null|true|false|1|1.5|\'a\'|"b"', ['null', 'true', 'false', '1', '1.5', "'a'", '"b"']];
        yield ['int | "a" | A<B<C, D>, E<F::*|G[]>>', ['int', '"a"', 'A<B<C, D>, E<F::*|G[]>>']];
        yield ['class-string<Foo>', ['class-string<Foo>']];
        yield ['A&B', ['A&B']];
        yield ['A & B', ['A & B']];
        yield ['array{1: bool, 2: bool}', ['array{1: bool, 2: bool}']];
        yield ['array{a: int|string, b?: bool}', ['array{a: int|string, b?: bool}']];
        yield ['array{\'a\': "a", "b"?: \'b\'}', ['array{\'a\': "a", "b"?: \'b\'}']];
        yield ['array { a : int | string , b ? : A<B, C> }', ['array { a : int | string , b ? : A<B, C> }']];
        yield ['callable(string)', ['callable(string)']];
        yield ['callable(string): bool', ['callable(string): bool']];
        yield ['callable(array<int, string>, array<int, Foo>): bool', ['callable(array<int, string>, array<int, Foo>): bool']];
        yield ['array<int, callable(string): bool>', ['array<int, callable(string): bool>']];
        yield ['callable(string): callable(int)', ['callable(string): callable(int)']];
        yield ['callable(string) : callable(int) : bool', ['callable(string) : callable(int) : bool']];
        yield ['TheCollection<callable(Foo, Bar,Baz): Foo[]>|string[]|null', ['TheCollection<callable(Foo, Bar,Baz): Foo[]>', 'string[]', 'null']];
        yield ['Closure(string)', ['Closure(string)']];
        yield ['array  <  int   , callable  (  string  )  :   bool  >', ['array  <  int   , callable  (  string  )  :   bool  >']];
    }

    /**
     * @param NamespaceUseAnalysis[] $namespaceUses
     *
     * @dataProvider provideCommonTypeCases
     */
    public function testGetCommonType(string $typesExpression, ?string $expectedCommonType, NamespaceAnalysis $namespace = null, array $namespaceUses = []): void
    {
        $expression = new TypeExpression($typesExpression, $namespace, $namespaceUses);
        static::assertSame($expectedCommonType, $expression->getCommonType());
    }

    public function provideCommonTypeCases(): \Generator
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
        static::assertSame($expectNullAllowed, $expression->allowsNull());
    }

    public function provideAllowsNullCases(): \Generator
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
}
