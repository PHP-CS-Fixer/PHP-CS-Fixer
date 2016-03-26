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

namespace PhpCsFixer\Tests\Runner;

use PhpCsFixer\Config;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Fixer;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\NullLinter;
use PhpCsFixer\Runner\Runner;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class FixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PhpCsFixer\Runner\Runner::fix
     * @covers PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatFixSuccessfully()
    {
        $config = Config::create()
            ->finder(Finder::create()->in(
                __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'fix'
            ))
            ->fixers(array(
                new Fixer\ClassNotation\VisibilityRequiredFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ))
            ->setUsingCache(false)
        ;

        $runner = new Runner(
            $config,
            new NullDiffer(),
            null,
            new ErrorsManager(),
            new NullLinter(),
            true
        );

        $changed = $runner->fix();
        $pathToInvalidFile = 'somefile.php';

        $this->assertCount(1, $changed);
        $this->assertCount(2, $changed[$pathToInvalidFile]);
        $this->assertSame(array('appliedFixers', 'diff'), array_keys($changed[$pathToInvalidFile]));
        $this->assertSame('visibility_required', $changed[$pathToInvalidFile]['appliedFixers'][0]);
    }

    /**
     * @covers PhpCsFixer\Runner\Runner::fix
     * @covers PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatFixInvalidFileReportsToErrorManager()
    {
        $config = Config::create()
            ->finder(Finder::create()->in(
                __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'invalid'
            ))
            ->fixers(array(
                new Fixer\ClassNotation\VisibilityRequiredFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ))
            ->setUsingCache(false)
        ;

        $errorsManager = new ErrorsManager();

        $runner = new Runner(
            $config,
            new NullDiffer(),
            null,
            $errorsManager,
            new Linter(),
            true
        );

        $changed = $runner->fix();
        $pathToInvalidFile = 'somefile.php';

        $this->assertCount(0, $changed);

        $errors = $errorsManager->getInvalidErrors();

        $this->assertCount(1, $errors);

        $error = $errors[0];

        $this->assertInstanceOf('PhpCsFixer\Error\Error', $error);

        $this->assertSame(Error::TYPE_INVALID, $error->getType());
        $this->assertSame($pathToInvalidFile, $error->getFilePath());
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
            ->willReturn(new \ArrayIterator(array()))
        ;

        $runner = new Runner(
            $config,
            new NullDiffer(),
            null,
            new ErrorsManager(),
            new NullLinter(),
            true
        );

        $runner->fix();
    }
}
