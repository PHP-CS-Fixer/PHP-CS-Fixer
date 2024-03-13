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
        foreach ($tokens as $index => $token) {
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
            if ($token->equals([T_STRING, 'Foo'])) {
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

        yield [true, '<?php #[Bar, Foo, Baz] class Qux {}'];

        yield [true, '<?php #[\Foo] class Bar {}'];

        yield [true, '<?php #[\Bar, \Foo] class Baz {}'];

        yield [true, '<?php #[Attr1(2 * (3 + 7)), Foo, Attr2((16 + 4) / 5 )] class Bar {}'];

        yield [true, '<?php #[Foo("(")] class Bar {}'];
    }

    public function testGetAttributesForNotAllowedElement(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Index 1 cannot have attributes.');

        AttributeAnalyzer::getAttributesForElement(
            Tokens::fromCode('<?php echo "foo";'),
            1
        );
    }

    /**
     * @param list<AttributeAnalysis> $attributeAnalysis
     *
     * @requires PHP 8.0
     *
     * @dataProvider provideGetAttributesForElementCases
     */
    public function testGetAttributesForElement(string $code, int $index, array $attributeAnalysis): void
    {
        $tokens = Tokens::fromCode($code);

        self::assertSame(
            serialize($attributeAnalysis),
            serialize(AttributeAnalyzer::getAttributesForElement($tokens, $index)),
        );
    }

    /**
     * @return iterable<array{string, int, list<AttributeAnalysis>}>
     */
    public static function provideGetAttributesForElementCases(): iterable
    {
        yield [
            '<?php #[Foo("(")] class AClass {}',
            8,
            [
                new AttributeAnalysis(1, 6),
            ],
        ];

        yield [
            '<?php #[Foo] #[Bar(1)] #[Baz(2+3+4+5)] class AClass {}',
            25,
            [
                new AttributeAnalysis(1, 3),
                new AttributeAnalysis(5, 10),
                new AttributeAnalysis(12, 23),
            ],
        ];

        yield [
            '<?php #[Foo] class AClass {} class BClass {}',
            5,
            [
                new AttributeAnalysis(1, 3),
            ],
        ];

        yield [
            '<?php #[Foo] class AClass {} class BClass {}',
            12,
            [],
        ];
    }
}
