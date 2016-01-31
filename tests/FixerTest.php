<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PhpCsFixer\Config;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Fixer;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\Linter\Linter;

/**
 * @internal
 */
final class FixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PhpCsFixer\Fixer::addConfig
     * @covers PhpCsFixer\Fixer::getConfigs
     */
    public function testThatCanAddAndGetConfigs()
    {
        $fixer = new Fixer();

        $configs = $fixer->getConfigs();

        $c1 = $this->getMock('PhpCsFixer\ConfigInterface');
        $c2 = $this->getMock('PhpCsFixer\ConfigInterface');

        $fixer->addConfig($c1);
        $fixer->addConfig($c2);

        $configs[] = $c1;
        $configs[] = $c2;

        $this->assertSame($configs, $fixer->getConfigs());
    }

    /**
     * @covers PhpCsFixer\Fixer::fix
     * @covers PhpCsFixer\Fixer::fixFile
     */
    public function testThatFixSuccessfully()
    {
        $fixer = new Fixer();
        $config = Config::create()
            ->finder(new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'fix'))
            ->fixers(array(
                new \PhpCsFixer\Fixer\PSR2\VisibilityRequiredFixer(),
                new \PhpCsFixer\Fixer\Symfony\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ))
            ->setUsingCache(false)
        ;

        $changed = $fixer->fix($config, true, true);
        $pathToInvalidFile = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'fix'.DIRECTORY_SEPARATOR.'somefile.php';

        $this->assertCount(1, $changed);
        $this->assertCount(2, $changed[$pathToInvalidFile]);
        $this->assertSame(array('appliedFixers', 'diff'), array_keys($changed[$pathToInvalidFile]));
        $this->assertSame('visibility_required', $changed[$pathToInvalidFile]['appliedFixers'][0]);
    }

    /**
     * @covers PhpCsFixer\Fixer::fix
     * @covers PhpCsFixer\Fixer::fixFile
     */
    public function testThatFixInvalidFileReportsToErrorManager()
    {
        $fixer = new Fixer();
        $fixer->setLinter(new Linter());

        $config = Config::create()
            ->finder(new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'invalid'))
            ->fixers(array(
                new \PhpCsFixer\Fixer\PSR2\VisibilityRequiredFixer(),
                new \PhpCsFixer\Fixer\Symfony\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ))
            ->setUsingCache(false)
        ;

        $changed = $fixer->fix($config, true, true);
        $pathToInvalidFile = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'invalid'.DIRECTORY_SEPARATOR.'somefile.php';

        $this->assertCount(0, $changed);

        $errors = $fixer->getErrorsManager()->getInvalidErrors();

        $this->assertCount(1, $errors);

        $error = $errors[0];

        $this->assertInstanceOf('PhpCsFixer\Error\Error', $error);

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
        $config = $this->getMockBuilder('PhpCsFixer\ConfigInterface')->getMock();

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
