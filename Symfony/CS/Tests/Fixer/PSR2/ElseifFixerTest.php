<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Fixer\All\ControlSpacesFixer;
use Symfony\CS\Fixer\PSR2\ElseifFixer;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ElseifFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::fix
     */
    public function testThatInvalidElseIfIsFixed()
    {
        $fixer = new ElseifFixer();

        $this->assertSame(
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            $fixer->fix($this->getTestFile(), '<?php if ($some) { $test = true; } else if ($some !== "test") { $test = false; }')
        );

        $this->assertSame(
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            $fixer->fix($this->getTestFile(), '<?php if ($some) { $test = true; } else  if ($some !== "test") { $test = false; }')
        );

        $this->assertSame(
            '<?php $js = \'if (foo.a) { foo.a = "OK"; } else if (foo.b) { foo.b = "OK"; }\';',
            $fixer->fix($this->getTestFile(), '<?php $js = \'if (foo.a) { foo.a = "OK"; } else if (foo.b) { foo.b = "OK"; }\';')
        );

        $this->assertSame(
            '<?php
if ($a) {
    $x = 1;
} elseif ($b) {
    $x = 2;
}',
            $fixer->fix(
                $this->getTestFile(),
                '<?php
if ($a) {
    $x = 1;
} else
if ($b) {
    $x = 2;
}'
            )
        );
    }

    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::getName
     */
    public function testThatHaveExpectedName()
    {
        $fixer = new ElseifFixer();

        $this->assertSame('elseif', $fixer->getName());
    }

    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::getDescription
     */
    public function testThatHaveDescription()
    {
        $fixer = new ElseifFixer();

        $this->assertNotEmpty($fixer->getDescription());
    }

    public function testThatAreDefinedInPSR2()
    {
        $fixer = new ElseIfFixer();
        $this->assertSame(FixerInterface::PSR2_LEVEL, $fixer->getLevel());
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
