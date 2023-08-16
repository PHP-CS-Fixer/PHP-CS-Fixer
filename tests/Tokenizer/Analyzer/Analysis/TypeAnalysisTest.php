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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer\Analysis;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis
 */
final class TypeAnalysisTest extends TestCase
{
    public function testName(): void
    {
        $analysis = new TypeAnalysis('string', 1, 2);
        self::assertSame('string', $analysis->getName());
        self::assertFalse($analysis->isNullable());

        $analysis = new TypeAnalysis('?\foo\bar', 1, 2);
        self::assertSame('\foo\bar', $analysis->getName());
        self::assertTrue($analysis->isNullable());
    }

    public function testStartIndex(): void
    {
        $analysis = new TypeAnalysis('string', 10, 20);
        self::assertSame(10, $analysis->getStartIndex());
    }

    public function testEndIndex(): void
    {
        $analysis = new TypeAnalysis('string', 1, 27);
        self::assertSame(27, $analysis->getEndIndex());
    }

    /**
     * @dataProvider provideReservedCases
     */
    public function testReserved(string $type, bool $expected): void
    {
        $analysis = new TypeAnalysis($type, 1, 2);
        self::assertSame($expected, $analysis->isReservedType());
    }

    public static function provideReservedCases(): iterable
    {
        yield ['array', true];

        yield ['bool', true];

        yield ['callable', true];

        yield ['float', true];

        yield ['int', true];

        yield ['iterable', true];

        yield ['mixed', true];

        yield ['never', true];

        yield ['null', true];

        yield ['object', true];

        yield ['resource', true];

        yield ['self', true];

        yield ['string', true];

        yield ['void', true];

        yield ['VOID', true];

        yield ['Void', true];

        yield ['voId', true];

        yield ['other', false];

        yield ['OTHER', false];

        yield ['numeric', false];
    }

    /**
     * @dataProvider provideIsNullableCases
     */
    public function testIsNullable(bool $expected, string $input): void
    {
        $analysis = new TypeAnalysis($input, 1, 2);
        self::assertSame($expected, $analysis->isNullable());
    }

    public static function provideIsNullableCases(): iterable
    {
        yield [false, 'string'];

        yield [true, '?string'];

        yield [false, 'String'];

        yield [true, '?String'];

        yield [false, '\foo\bar'];

        yield [true, '?\foo\bar'];

        if (\PHP_VERSION_ID >= 8_00_00) {
            yield [false, 'string|int'];

            yield [true, 'string|null'];

            yield [true, 'null|string'];

            yield [true, 'string|NULL'];

            yield [true, 'NULL|string'];

            yield [true, 'string|int|null'];

            yield [true, 'null|string|int'];

            yield [true, 'string|null|int'];

            yield [true, 'string|int|NULL'];

            yield [true, 'NULL|string|int'];

            yield [true, 'string|NULL|int'];

            yield [false, 'string|\foo\bar'];

            yield [true, 'string|\foo\bar|null'];

            yield [true, 'null|string|\foo\bar'];

            yield [true, 'string|null|\foo\bar'];

            yield [true, 'string |null| int'];

            yield [true, 'string| null |int'];

            yield [true, 'string | null | int'];

            yield [false, 'Null2|int'];

            yield [false, 'string|Null2'];

            yield [false, 'string |Null2'];

            yield [false, 'Null2| int'];

            yield [false, 'string | Null2 | int'];
        }

        if (\PHP_VERSION_ID >= 8_01_00) {
            yield [false, '\foo\bar&\foo\baz'];

            yield [false, '\foo\bar & \foo\baz'];

            yield [false, '\foo\bar&Null2'];
        }

        if (\PHP_VERSION_ID >= 8_02_00) {
            yield [true, '(\foo\bar&\foo\baz)|null'];

            yield [true, '(\foo\bar&\foo\baz) | null'];

            yield [false, '(\foo\bar&\foo\baz)|Null2'];

            yield [true, 'null'];

            yield [true, 'Null'];

            yield [true, 'NULL'];
        }
    }
}
