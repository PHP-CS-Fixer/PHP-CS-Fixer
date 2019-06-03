<?php

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
    public function testUniqueValues()
    {
        $constants = $this->getConstants();
        static::assertSame($constants, array_flip(array_flip($constants)), 'Values of CT::T_* constants must be unique.');
    }

    /**
     * @param string $name
     * @param int    $value
     *
     * @dataProvider provideConstantsCases
     */
    public function testHas($name, $value)
    {
        static::assertTrue(CT::has($value));
    }

    public function testHasNotExists()
    {
        static::assertFalse(CT::has(123));
    }

    /**
     * @param string $name
     * @param int    $value
     *
     * @dataProvider provideConstantsCases
     */
    public function testGetName($name, $value)
    {
        static::assertSame('CT::'.$name, CT::getName($value));
    }

    public function testGetNameNotExists()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No custom token was found for "123".');

        CT::getName(123);
    }

    /**
     * @param string $name
     * @param int    $value
     *
     * @dataProvider provideConstantsCases
     */
    public function testConstants($name, $value)
    {
        static::assertGreaterThan(10000, $value);
        static::assertNull(@\constant($name), 'The CT name must not use native T_* name.');
    }

    public function provideConstantsCases()
    {
        $cases = [];

        foreach ($this->getConstants() as $name => $value) {
            $cases[] = [$name, $value];
        }

        return $cases;
    }

    private function getConstants()
    {
        static $constants;

        if (null === $constants) {
            $reflection = new \ReflectionClass(\PhpCsFixer\Tokenizer\CT::class);
            $constants = $reflection->getConstants();
        }

        return $constants;
    }
}
