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

use PHPUnit\Framework\TestCase;

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
     * @dataProvider provideCTs
     */
    public function testConstants($name, $value)
    {
        $this->assertGreaterThan(10000, $value);
        $this->assertNull(@constant($name), 'The CT name must not use native T_* name.');
    }

    public function provideCTs()
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
