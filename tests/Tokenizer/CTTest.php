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
        $this->assertSame($constants, array_flip(array_flip($constants)), 'Values of CT::T_* constants must be unique.');
    }

    /**
     * @param string $name
     * @param int    $value
     *
     * @dataProvider provideConstantsCases
     */
    public function testHas($name, $value)
    {
        $this->assertTrue(CT::has($value));
    }

    public function testHasNotExists()
    {
        $this->assertFalse(CT::has(123));
    }

    /**
     * @param string $name
     * @param int    $value
     *
     * @dataProvider provideConstantsCases
     */
    public function testGetName($name, $value)
    {
        $this->assertSame('CT::'.$name, CT::getName($value));
    }

    public function testGetNameNotExists()
    {
        $this->setExpectedException('InvalidArgumentException', 'No custom token was found for "123".');

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
        $this->assertGreaterThan(10000, $value);
        $this->assertNull(@constant($name), 'The CT name must not use native T_* name.');
    }

    public function provideConstantsCases()
    {
        $cases = array();

        foreach ($this->getConstants() as $name => $value) {
            $cases[] = array($name, $value);
        }

        return $cases;
    }

    private function getConstants()
    {
        static $constants;

        if (null === $constants) {
            $reflection = new \ReflectionClass('PhpCsFixer\Tokenizer\CT');
            $constants = $reflection->getConstants();
        }

        return $constants;
    }
}
