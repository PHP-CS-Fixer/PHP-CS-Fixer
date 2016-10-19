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

use PhpCsFixer\Tokenizer\CT;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class CTTest extends \PHPUnit_Framework_TestCase
{
    public function testUniqueValues()
    {
        $constants = $this->getConstants();
        $this->assertSame($constants, array_flip(array_flip($constants)), 'Values of CT::T_* constants must be unique.');
    }

    /**
     * @dataProvider provideCTs
     */
    public function testConstants($name, $value)
    {
        $this->assertGreaterThan(10000, $value);
        $this->assertNull(@constant($name), 'The CT name must not use native T_* name');
    }

    public function provideCTs()
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
            $reflection = new \ReflectionClass("PhpCsFixer\Tokenizer\CT");
            $constants = $reflection->getConstants();
        }

        return $constants;
    }
}
