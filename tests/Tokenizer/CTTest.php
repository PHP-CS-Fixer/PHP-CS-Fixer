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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\CT
 */
final class CTTest extends TestCase
{
    public function testUniqueValues(): void
    {
        $constants = self::getConstants();
        self::assertSame($constants, array_flip(array_flip($constants)), 'Values of CT::T_* constants must be unique.');
    }

    /**
     * @dataProvider provideConstantsCases
     */
    public function testHas(string $name, int $value): void
    {
        self::assertTrue(CT::has($value));
    }

    public function testHasNotExists(): void
    {
        self::assertFalse(CT::has(123));
    }

    /**
     * @dataProvider provideConstantsCases
     */
    public function testGetName(string $name, int $value): void
    {
        self::assertSame('CT::'.$name, CT::getName($value));
    }

    public function testGetNameNotExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No custom token was found for "123".');

        CT::getName(123);
    }

    /**
     * @dataProvider provideConstantsCases
     */
    public function testConstants(string $name, int $value): void
    {
        self::assertGreaterThan(10000, $value);
        self::assertFalse(\defined($name), 'The CT name must not use native T_* name.');
    }

    public static function provideConstantsCases(): iterable
    {
        foreach (self::getConstants() as $name => $value) {
            yield [$name, $value];
        }
    }

    /**
     * @return array<string, int>
     */
    private static function getConstants(): array
    {
        static $constants;

        if (null === $constants) {
            $reflection = new \ReflectionClass(CT::class);
            $constants = $reflection->getConstants();
        }

        return $constants;
    }
}
