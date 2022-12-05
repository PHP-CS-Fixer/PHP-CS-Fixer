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
        static::assertSame('string', $analysis->getName());
        static::assertFalse($analysis->isNullable());

        $analysis = new TypeAnalysis('?\foo\bar', 1, 2);
        static::assertSame('\foo\bar', $analysis->getName());
        static::assertTrue($analysis->isNullable());
    }

    public function testStartIndex(): void
    {
        $analysis = new TypeAnalysis('string', 10, 20);
        static::assertSame(10, $analysis->getStartIndex());
    }

    public function testEndIndex(): void
    {
        $analysis = new TypeAnalysis('string', 1, 27);
        static::assertSame(27, $analysis->getEndIndex());
    }

    /**
     * @dataProvider provideReservedCases
     */
    public function testReserved(string $type, bool $expected): void
    {
        $analysis = new TypeAnalysis($type, 1, 2);
        static::assertSame($expected, $analysis->isReservedType());
    }

    public static function provideReservedCases(): array
    {
        return [
            ['array', true],
            ['bool', true],
            ['callable', true],
            ['float', true],
            ['int', true],
            ['iterable', true],
            ['mixed', true],
            ['never', true],
            ['numeric', true],
            ['object', true],
            ['other', false],
            ['resource', true],
            ['self', true],
            ['string', true],
            ['void', true],
        ];
    }
}
