<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Config\Config;

class FixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\CS\Fixer::sortFixers
     */
    public function testThatFixersAreSorted()
    {
        $fixer = new Fixer();

        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f1->expects($this->any())->method('getPriority')->will($this->returnValue(0));

        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $f2->expects($this->any())->method('getPriority')->will($this->returnValue(-10));

        $f3 = $this->getMock('Symfony\CS\FixerInterface');
        $f3->expects($this->any())->method('getPriority')->will($this->returnValue(10));

        $f4 = $this->getMock('Symfony\CS\FixerInterface');
        $f4->expects($this->any())->method('getPriority')->will($this->returnValue(-10));

        $fixer->addFixer($f1);
        $fixer->addFixer($f2);
        $fixer->addFixer($f3);
        $fixer->addFixer($f4);

        $this->assertSame(array($f3, $f1, $f4, $f2), $fixer->getFixers());
    }

    /**
     * @covers Symfony\CS\Fixer::registerBuiltInFixers
     */
    public function testThatRegisterBuiltInFixers()
    {
        $fixer = new Fixer();

        $this->assertCount(0, $fixer->getFixers());
        $fixer->registerBuiltInFixers();
        $this->assertGreaterThan(0, count($fixer->getFixers()));
    }

    /**
     * @covers Symfony\CS\Fixer::registerBuiltInConfigs
     */
    public function testThatRegisterBuiltInConfigs()
    {
        $fixer = new Fixer();

        $this->assertCount(0, $fixer->getConfigs());
        $fixer->registerBuiltInConfigs();
        $this->assertGreaterThan(0, count($fixer->getConfigs()));
    }

    /**
     * @covers Symfony\CS\Fixer::addFixer
     * @covers Symfony\CS\Fixer::getFixers
     */
    public function testThatCanAddAndGetFixers()
    {
        $fixer = new Fixer();

        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $fixer->addFixer($f1);
        $fixer->addFixer($f2);

        $this->assertSame(array($f2, $f1), $fixer->getFixers());
    }

    /**
     * @covers Symfony\CS\Fixer::addConfig
     * @covers Symfony\CS\Fixer::getConfigs
     */
    public function testThatCanAddAndGetConfigs()
    {
        $fixer = new Fixer();

        $c1 = $this->getMock('Symfony\CS\ConfigInterface');
        $c2 = $this->getMock('Symfony\CS\ConfigInterface');
        $fixer->addConfig($c1);
        $fixer->addConfig($c2);

        $this->assertSame(array($c1, $c2), $fixer->getConfigs());
    }

    /**
     * @covers Symfony\CS\Fixer::fix
     * @covers Symfony\CS\Fixer::fixFile
     * @covers Symfony\CS\Fixer::prepareFixers
     */
    public function testThatFixSuccessfully()
    {
        $fixer = new Fixer();
        $fixer->addFixer(new \Symfony\CS\Fixer\VisibilityFixer());
        $fixer->addFixer(new \Symfony\CS\Fixer\Psr0Fixer()); //will be ignored cause of test keyword in namespace

        $config = Config::create()->finder(new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'));
        $config->fixers($fixer->getFixers());

        $changed = $fixer->fix($config, true, true);
        $pathToInvalidFile = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'somefile.php';

        $this->assertCount(1, $changed);
        $this->assertCount(2, $changed[$pathToInvalidFile]);
        $this->assertSame(array('appliedFixers', 'diff'), array_keys($changed[$pathToInvalidFile]));
        $this->assertSame('visibility', $changed[$pathToInvalidFile]['appliedFixers'][0]);
    }

    /**
     * @covers Symfony\CS\Fixer::getLevelAsString
     * @dataProvider getFixerLevels
     */
    public function testThatCanGetFixerLevelString($level, $expectedLevelString)
    {
        $fixer = $this->getMock('Symfony\CS\FixerInterface');
        $fixer->expects($this->any())->method('getLevel')->will($this->returnValue($level));

        $this->assertSame($expectedLevelString, Fixer::getLevelAsString($fixer));
    }

    public static function getFixerLevels()
    {
        return array(
            array(FixerInterface::PSR0_LEVEL, 'PSR-0'),
            array(FixerInterface::PSR1_LEVEL, 'PSR-1'),
            array(FixerInterface::PSR2_LEVEL, 'PSR-2'),
            array(FixerInterface::ALL_LEVEL, 'all'),
        );
    }
}
