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
    public function testThatInvalidElseIfInSingleLineIsFixed()
    {
        $fixer = new ElseifFixer();

        $this->assertSame(
            'if ($some) { $test = true } elseif ($some != "test") { $test = false; }',
            $fixer->fix($this->getFileMock(), 'if ($some) { $test = true } else if ($some != "test") { $test = false; }'
        ));
    }

    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::fix
     */
    public function testThatInvalidElseIfInMultiLineIsFixed()
    {
        $fixer      = new ElseifFixer();
        $invalid    = <<<'EOD'
if ($some) {
    $test = true
} else
if ($some != "test") {
    $test = false;
}
EOD;
        $expected   = <<<'EOD'
if ($some) {
    $test = true
} elseif ($some != "test") {
    $test = false;
}
EOD;

        $this->assertSame(
            $expected,
            $fixer->fix($this->getFileMock(), $invalid)
        );
    }
    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::fix
     */
    public function testThatInvalidElseIfInMultiLineWithoutBracketsIsFixed()
    {
        $fixer      = new ElseifFixer();
        $invalid    = <<<'EOD'
$some = 'test';
if (true === $some)
    $test = 'a';
else
if ($some != "test")
    $test = 'b';
else
    $test = 'c';

echo $test; // "c"
EOD;
        $expected   = <<<'EOD'
$some = 'test';
if (true === $some)
    $test = 'a';
elseif ($some != "test")
    $test = 'b';
else
    $test = 'c';

echo $test; // "c"
EOD;

        $this->assertSame(
            $expected,
            $fixer->fix($this->getFileMock(), $invalid)
        );
    }

    /**
     * @covers Symfony\CS\Fixer\ElseifFixer::getName
     */
    public function testThatHaveExpectedName()
    {
        $fixer = new ElseifFixer();

        $this->assertEquals('elseif', $fixer->getName());
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
        $phpFile = $this->getFileMock();
        $phpFile->expects($this->any())
            ->method('getFilename')
            ->will($this->returnValue('file.php'));

        $otherFile = $this->getFileMock();
        $otherFile->expects($this->any())
            ->method('getFilename')
            ->will($this->returnValue('file.js'));

       $fixer = new ElseIfFixer();

       $this->assertTrue($fixer->supports($phpFile));
       $this->assertFalse($fixer->supports($otherFile));
    }

    public function testThatAreDefinedInPSR2()
    {
       $fixer = new ElseIfFixer();
       $this->assertSame(FixerInterface::PSR2_LEVEL, $fixer->getLevel());
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
