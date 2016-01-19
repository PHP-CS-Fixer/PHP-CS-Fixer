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

use Symfony\CS\Config;
use Symfony\CS\Error\Error;
use Symfony\CS\Fixer;
use Symfony\CS\FixerFactory;
use Symfony\CS\FixerInterface;
use Symfony\CS\Linter\Linter;

/**
 * @internal
 */
final class FixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\CS\Fixer::addConfig
     * @covers Symfony\CS\Fixer::getConfigs
     */
    public function testThatCanAddAndGetConfigs()
    {
        $fixer = new Fixer();

        $configs = $fixer->getConfigs();

        $c1 = $this->getMock('Symfony\CS\ConfigInterface');
        $c2 = $this->getMock('Symfony\CS\ConfigInterface');

        $fixer->addConfig($c1);
        $fixer->addConfig($c2);

        $configs[] = $c1;
        $configs[] = $c2;

        $this->assertSame($configs, $fixer->getConfigs());
    }

    /**
     * @covers Symfony\CS\Fixer::fix
     * @covers Symfony\CS\Fixer::fixFile
     */
    public function testThatFixSuccessfully()
    {
        $fixer = new Fixer();
        $config = Config::create()
            ->finder(new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'fix'))
            ->fixers(array(
                new \Symfony\CS\Fixer\PSR2\VisibilityFixer(),
                new \Symfony\CS\Fixer\Symfony\UnusedUseFixer(), // will be ignored cause of test keyword in namespace
            ))
            ->setUsingCache(false)
        ;

        $changed = $fixer->fix($config, true, true);
        $pathToInvalidFile = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'fix'.DIRECTORY_SEPARATOR.'somefile.php';

        $this->assertCount(1, $changed);
        $this->assertCount(2, $changed[$pathToInvalidFile]);
        $this->assertSame(array('appliedFixers', 'diff'), array_keys($changed[$pathToInvalidFile]));
        $this->assertSame('visibility', $changed[$pathToInvalidFile]['appliedFixers'][0]);
    }

    /**
     * @covers Symfony\CS\Fixer::fix
     * @covers Symfony\CS\Fixer::fixFile
     */
    public function testThatFixInvalidFileReportsToErrorManager()
    {
        $fixer = new Fixer();
        $fixer->setLinter(new Linter());

        $config = Config::create()
            ->finder(new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'invalid'))
            ->fixers(array(
                new \Symfony\CS\Fixer\PSR2\VisibilityFixer(),
                new \Symfony\CS\Fixer\Symfony\UnusedUseFixer(), // will be ignored cause of test keyword in namespace
            ))
            ->setUsingCache(false)
        ;

        $changed = $fixer->fix($config, true, true);
        $pathToInvalidFile = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'invalid'.DIRECTORY_SEPARATOR.'somefile.php';

        $this->assertCount(0, $changed);

        $errors = $fixer->getErrorsManager()->getInvalidErrors();

        $this->assertCount(1, $errors);

        $error = $errors[0];

        $this->assertInstanceOf('Symfony\CS\Error\Error', $error);

        $this->assertSame(Error::TYPE_INVALID, $error->getType());
        $this->assertSame($pathToInvalidFile, $error->getFilePath());
    }

    /**
     * @dataProvider provideFixersDescriptionConsistencyCases
     */
    public function testFixersDescriptionConsistency(FixerInterface $fixer)
    {
        $this->assertRegExp('/^[A-Z@].*\.$/', $fixer->getDescription(), 'Description must start with capital letter or an @ and end with dot.');
    }

    public function provideFixersDescriptionConsistencyCases()
    {
        foreach ($this->getAllFixers() as $fixer) {
            $cases[] = array($fixer);
        }

        return $cases;
    }

    public function testCanFixWithConfigInterfaceImplementation()
    {
        $config = $this->getMockBuilder('Symfony\CS\ConfigInterface')->getMock();

        $config
            ->expects($this->any())
            ->method('getFixers')
            ->willReturn(array())
        ;

        $config
            ->expects($this->any())
            ->method('getRules')
            ->willReturn(array())
        ;

        $config
            ->expects($this->any())
            ->method('getFinder')
            ->willReturn(array())
        ;

        $fixer = new Fixer();

        $fixer->fix($config);
    }

    /**
     * @dataProvider provideFixersForFinalCheckCases
     */
    public function testFixersAreFinal(\ReflectionClass $class)
    {
        $this->assertTrue($class->isFinal());
    }

    public function provideFixersForFinalCheckCases()
    {
        $cases = array();

        foreach ($this->getAllFixers() as $fixer) {
            $cases[] = array(new \ReflectionClass($fixer));
        }

        return $cases;
    }

    private function getAllFixers()
    {
        $factory = new FixerFactory();

        return $factory->registerBuiltInFixers()->getFixers();
    }
}
