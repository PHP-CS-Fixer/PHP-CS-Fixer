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

        $fxPrototypes = array(
            array('getPriority' =>   0),
            array('getPriority' => -10),
            array('getPriority' =>  10),
            array('getPriority' => -10),
        );

        $fxs = array();

        foreach ($fxPrototypes as $fxPrototype) {
            $fx = $this->getMock('Symfony\CS\FixerInterface');
            $fx->expects($this->any())->method('getPriority')->willReturn($fxPrototype['getPriority']);

            $fixer->addFixer($fx);
            $fxs[] = $fx;
        }

        // There are no rules that forces $fxs[1] to be prioritized before $fxs[3]. We should not test against that
        $this->assertSame(array($fxs[2], $fxs[0]), array_slice($fixer->getFixers(), 0, 2));
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

        $this->assertTrue(in_array($f1, $fixer->getFixers()));
        $this->assertTrue(in_array($f2, $fixer->getFixers()));
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
        $fixer->addFixer(new \Symfony\CS\Fixer\PSR2\VisibilityFixer());
        $fixer->addFixer(new \Symfony\CS\Fixer\PSR0\Psr0Fixer()); //will be ignored cause of test keyword in namespace

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

    public function testFixersPriorityEdgeFixers()
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();
        $fixers = $fixer->getFixers();

        $this->assertSame('encoding', $fixers[0]->getName());
        $this->assertSame('eof_ending', $fixers[count($fixers) - 1]->getName());
    }

    /**
     * @dataProvider getFixersPriorityCases
     */
    public function testFixersPriority(FixerInterface $first, FixerInterface $second)
    {
        $this->assertLessThan($first->getPriority(), $second->getPriority());
    }

    public function getFixersPriorityCases()
    {
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();

        $fixers = array();

        foreach ($fixer->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        return array(
            array($fixers['php_closing_tag'], $fixers['short_tag']),
            array($fixers['multiple_use'], $fixers['unused_use']),
            array($fixers['multiple_use'], $fixers['ordered_use']),
            array($fixers['concat_without_spaces'], $fixers['concat_with_spaces']),
            array($fixers['elseif'], $fixers['braces']),
        );
    }

    public static function getFixerLevels()
    {
        return array(
            array(FixerInterface::PSR0_LEVEL, 'PSR-0'),
            array(FixerInterface::PSR1_LEVEL, 'PSR-1'),
            array(FixerInterface::PSR2_LEVEL, 'PSR-2'),
            array(FixerInterface::SYMFONY_LEVEL, 'symfony'),
            array(FixerInterface::CONTRIB_LEVEL, 'contrib'),
        );
    }
}
