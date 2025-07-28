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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\AttributeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer
 */
final class AttributeAnalyzerTest extends TestCase
{
    /**
     * Check it's not crashing for PHP lower than 8.0 as other tests run for PHP 8.0 only.
     */
    public function testNotAnAttribute(): void
    {
        $tokens = Tokens::fromCode('<?php class Foo { private $bar; }');
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            self::assertFalse(AttributeAnalyzer::isAttribute($tokens, $index));
        }
    }

    /**
     * @requires     PHP 8.0
     *
     * @dataProvider provideIsAttributeCases
     */
    public function testIsAttribute(bool $isInAttribute, string $code): void
    {
        $tokens = Tokens::fromCode($code);

        foreach ($tokens as $index => $token) {
            if ($token->equals([\T_STRING, 'Foo'])) {
                if (isset($testedIndex)) {
                    self::fail('Test is run against index of "Foo", multiple occurrences found.');
                }
                $testedIndex = $index;
            }
        }
        if (!isset($testedIndex)) {
            self::fail('Test is run against index of "Foo", but it was not found in the code.');
        }

        self::assertSame($isInAttribute, AttributeAnalyzer::isAttribute($tokens, $testedIndex));
    }

    /**
     * Test case requires to having "Foo" as it will be searched for to test its index.
     *
     * @return iterable<int, array{bool, string}>
     */
    public static function provideIsAttributeCases(): iterable
    {
        yield [false, '<?php Foo; #[Attr] class Bar {}'];

        yield [false, '<?php Foo(); #[Attr] class Bar {};'];

        yield [false, '<?php \Foo(); #[Attr] class Bar {}'];

        yield [false, '<?php #[Attr] class Foo {};'];

        yield [false, '<?php #[Attr] class Bar {}; Foo;'];

        yield [false, '<?php #[Attr] class Bar {}; \Foo();'];

        yield [false, '<?php #[Attr] class Bar { const Foo = 42; };'];

        yield [false, '<?php #[Attr] class Bar { function Foo() {} };'];

        yield [false, '<?php #[Attr(Foo)] class Bar {}'];

        yield [false, '<?php #[Attr(Foo::Bar)] class Baz {}'];

        yield [false, '<?php #[Attr(Bar::Foo)] class Baz {}'];

        yield [false, '<?php #[Attr(1, 2, Foo)] class Bar {}'];

        yield [false, '<?php #[Attr(Foo, 1, 2)] class Bar {}'];

        yield [false, '<?php #[Attr(1, 2, Foo, 3, 4)] class Bar {}'];

        yield [false, '<?php #[Attr(2 * (3 + 7), 2, Foo)] class Bar {}'];

        yield [false, '<?php #[Attr(2 * (3 + 7), 2, Foo, 5)] class Bar {}'];

        yield [false, '<?php #[Attr(2 * (3 + 7), 2, Foo, (16 + 4) / 5 )] class Bar {}'];

        yield [false, '<?php #[Attr] function Foo() {};'];

        yield [false, '<?php #[Attr("Foo()")] class Foo {}'];

        yield [true, '<?php #[Foo] class Bar {}'];

        yield [true, '<?php #[Foo, Bar] class Baz {}'];

        yield [true, '<?php #[Bar, Foo] class Baz {}'];

        yield [true, '<?php #[Bar, Foo, Baz] class Qux {}'];

        yield [true, '<?php #[Foo(), Bar()] class Baz {}'];

        yield [true, '<?php #[Bar(), Foo()] class Baz {}'];

        yield [true, '<?php #[Bar(), Foo(), Baz()] class Qux {}'];

        yield [true, '<?php #[Bar(), Foo, Baz()] class Qux {}'];

        yield [true, '<?php #[\Foo] class Bar {}'];

        yield [true, '<?php #[\Bar, \Foo] class Baz {}'];

        yield [true, '<?php #[Attr1(2 * (3 + 7)), Foo, Attr2((16 + 4) / 5 )] class Bar {}'];

        yield [true, '<?php #[Foo("(")] class Bar {}'];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideGetAttributeDeclarationsCases
     *
     * @param list<AttributeAnalysis> $expectedAnalyses
     */
    public function testGetAttributeDeclarations(string $code, int $startIndex, array $expectedAnalyses): void
    {
        $tokens = Tokens::fromCode($code);
        $actualAnalyses = AttributeAnalyzer::collect($tokens, $startIndex);

        foreach ($expectedAnalyses as $expectedAnalysis) {
            self::assertSame(\T_ATTRIBUTE, $tokens[$expectedAnalysis->getOpeningBracketIndex()]->getId());
            self::assertSame(CT::T_ATTRIBUTE_CLOSE, $tokens[$expectedAnalysis->getClosingBracketIndex()]->getId());
        }

        self::assertSame(
            serialize($expectedAnalyses),
            serialize($actualAnalyses),
        );
    }

    /**
     * @return iterable<string, array{0: string, 1: int, 2: list<AttributeAnalysis>}>
     */
    public static function provideGetAttributeDeclarationsCases(): iterable
    {
        yield 'multiple #[] in a multiline group' => [
            '<?php
            /**
             * Start docblock
             */
            #[AB\Baz(prop: \'baz\')]
            #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')]
            #[\A\B\Qux()]
            #[BarAlias(3)]
            /**
             * Corge docblock
             */
            #[Corge]
            #[Foo(4, \'baz qux\')]
            /**
             * End docblock
             */
            function foo() {}
            ',
            4,
            [
                new AttributeAnalysis(4, 15, 4, 14, [[
                    'start' => 5,
                    'end' => 13,
                    'name' => 'AB\Baz',
                ]]),
                new AttributeAnalysis(16, 49, 16, 48, [[
                    'start' => 17,
                    'end' => 47,
                    'name' => 'A\B\Quux',
                ]]),
                new AttributeAnalysis(50, 60, 50, 59, [[
                    'start' => 51,
                    'end' => 58,
                    'name' => '\A\B\Qux',
                ]]),
                new AttributeAnalysis(61, 67, 61, 66, [[
                    'start' => 62,
                    'end' => 65,
                    'name' => 'BarAlias',
                ]]),
                new AttributeAnalysis(68, 73, 70, 72, [[
                    'start' => 71,
                    'end' => 71,
                    'name' => 'Corge',
                ]]),
                new AttributeAnalysis(74, 83, 74, 82, [[
                    'start' => 75,
                    'end' => 81,
                    'name' => 'Foo',
                ]]),
            ],
        ];

        yield 'multiple #[] in a single line group' => [
            '<?php
            /** Start docblock */#[AB\Baz(prop: \'baz\')] #[A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\')] #[\A\B\Qux()] #[BarAlias(3)] /** Corge docblock */#[Corge] #[Foo(4, \'baz qux\')]/** End docblock */
            function foo() {}
            ',
            3,
            [
                new AttributeAnalysis(3, 14, 3, 13, [[
                    'start' => 4,
                    'end' => 12,
                    'name' => 'AB\Baz',
                ]]),
                new AttributeAnalysis(15, 48, 15, 47, [[
                    'start' => 16,
                    'end' => 46,
                    'name' => 'A\B\Quux',
                ]]),
                new AttributeAnalysis(49, 59, 49, 58, [[
                    'start' => 50,
                    'end' => 57,
                    'name' => '\A\B\Qux',
                ]]),
                new AttributeAnalysis(60, 66, 60, 65, [[
                    'start' => 61,
                    'end' => 64,
                    'name' => 'BarAlias',
                ]]),
                new AttributeAnalysis(67, 71, 68, 70, [[
                    'start' => 69,
                    'end' => 69,
                    'name' => 'Corge',
                ]]),
                new AttributeAnalysis(72, 80, 72, 80, [[
                    'start' => 73,
                    'end' => 79,
                    'name' => 'Foo',
                ]]),
            ],
        ];

        yield 'comma-separated attributes in a multiline #[]' => [
            '<?php
            #[
                /*
                 * AB\Baz comment
                 */
                AB\Baz(prop: \'baz\'),
                A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'),
                \A\B\Qux(),
                BarAlias(3),
                /*
                 * Corge comment
                 */
                Corge,
                /**
                 * Foo docblock
                 */
                Foo(4, \'baz qux\'),
            ]
            function foo() {}
            ',
            2,
            [
                new AttributeAnalysis(2, 83, 2, 82, [[
                    'start' => 3,
                    'end' => 14,
                    'name' => 'AB\Baz',
                ], [
                    'start' => 16,
                    'end' => 47,
                    'name' => 'A\B\Quux',
                ], [
                    'start' => 49,
                    'end' => 57,
                    'name' => '\A\B\Qux',
                ], [
                    'start' => 59,
                    'end' => 63,
                    'name' => 'BarAlias',
                ], [
                    'start' => 65,
                    'end' => 68,
                    'name' => 'Corge',
                ], [
                    'start' => 70,
                    'end' => 79,
                    'name' => 'Foo',
                ]]),
            ],
        ];

        yield 'comma-separated attributes in a single line #[]' => [
            '<?php
            #[/* AB\Baz comment */AB\Baz(prop: \'baz\'), A\B\Quux(prop1: [1, 2, 4], prop2: true, prop3: \'foo bar\'), \A\B\Qux(), BarAlias(3), /* Corge comment */Corge, /** Foo docblock */Foo(4, \'baz qux\')]
            function foo() {}
            ',
            2,
            [
                new AttributeAnalysis(2, 77, 2, 76, [[
                    'start' => 3,
                    'end' => 12,
                    'name' => 'AB\Baz',
                ], [
                    'start' => 14,
                    'end' => 45,
                    'name' => 'A\B\Quux',
                ], [
                    'start' => 47,
                    'end' => 55,
                    'name' => '\A\B\Qux',
                ], [
                    'start' => 57,
                    'end' => 61,
                    'name' => 'BarAlias',
                ], [
                    'start' => 63,
                    'end' => 65,
                    'name' => 'Corge',
                ], [
                    'start' => 67,
                    'end' => 75,
                    'name' => 'Foo',
                ]]),
            ],
        ];
    }

    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideGetAttributeDeclarations81Cases
     *
     * @param list<AttributeAnalysis> $expectedAnalyses
     */
    public function testGetAttributeDeclarations81(string $code, int $startIndex, array $expectedAnalyses): void
    {
        $this->testGetAttributeDeclarations($code, $startIndex, $expectedAnalyses);
    }

    /**
     * @return iterable<string, array{0: string, 1: int, 2: list<AttributeAnalysis>}>
     */
    public static function provideGetAttributeDeclarations81Cases(): iterable
    {
        yield 'multiple #[] in a group, including `new` in arguments' => [
            '<?php
                #[AB\Baz(prop: \'baz\')]
                #[\A\B\Qux(prop: new P\R())]
                #[Corge]
                function foo() {}
                ',
            2,
            [
                new AttributeAnalysis(2, 13, 2, 12, [[
                    'start' => 3,
                    'end' => 11,
                    'name' => 'AB\Baz',
                ]]),
                new AttributeAnalysis(14, 34, 14, 33, [[
                    'start' => 15,
                    'end' => 32,
                    'name' => '\A\B\Qux',
                ]]),
                new AttributeAnalysis(35, 38, 35, 37, [[
                    'start' => 36,
                    'end' => 36,
                    'name' => 'Corge',
                ]]),
            ],
        ];

        yield 'comma-separated attributes in single #[] group, including `new` in arguments' => [
            '<?php
             #[
                 AB\Baz(prop: \'baz\'),
                 \A\B\Qux(prop: new P\R()),
                 Corge,
             ]
             function foo() {}
             ',
            2,
            [
                new AttributeAnalysis(2, 39, 2, 38, [[
                    'start' => 3,
                    'end' => 12,
                    'name' => 'AB\Baz',
                ], [
                    'start' => 14,
                    'end' => 32,
                    'name' => '\A\B\Qux',
                ], [
                    'start' => 34,
                    'end' => 35,
                    'name' => 'Corge',
                ]]),
            ],
        ];
    }
}
