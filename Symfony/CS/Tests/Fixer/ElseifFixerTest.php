<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Fixer\ControlSpacesFixer;
use Symfony\CS\Fixer\ElseifFixer;

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
            'if ($some) { $test = true } elseif ($some !== "test") { $test = false; }',
            $fixer->fix($this->getTestFile(), 'if ($some) { $test = true } else if ($some !== "test") { $test = false; }'
        ));

        $this->assertSame(
            'if ($some) { $test = true } elseif ($some !== "test") { $test = false; }',
            $fixer->fix($this->getTestFile(), 'if ($some) { $test = true } else  if ($some !== "test") { $test = false; }'
        ));
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

    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::getPriority
     */
    public function testThatWillBeRunAfterControlSpacesFixer()
    {
        $fixer = new Fixer();

        $elseIfFixer = new ElseIfFixer();
        $controlSpacesFixer = new ControlSpacesFixer();

        $fixer->addFixer($controlSpacesFixer);
        $fixer->addFixer($elseIfFixer);

        $this->assertSame(array($controlSpacesFixer, $elseIfFixer), $fixer->getFixers());
    }

    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::supports
     */
    public function testThatOnlyPHPFilesAreSupported()
    {
        $phpFile = $this->getTestFile();

        $otherFile = $this->getTestFile(__DIR__ . '/../../../../README.rst');

        $fixer = new ElseIfFixer();

        $this->assertTrue($fixer->supports($phpFile));
        $this->assertFalse($fixer->supports($otherFile));
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
