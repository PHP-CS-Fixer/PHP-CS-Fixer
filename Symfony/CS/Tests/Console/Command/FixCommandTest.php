<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\Console\Command\FixCommand;
use Symfony\CS\Error\Error;
use Symfony\CS\Error\ErrorsManager;
use Symfony\CS\Fixer;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class FixCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandHasCacheFileOption()
    {
        $command = new FixCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('cache-file'));

        $option = $definition->getOption('cache-file');

        $this->assertNull($option->getShortcut());
        $this->assertTrue($option->isValueRequired());
        $this->assertSame('The path to the cache file', $option->getDescription());
        $this->assertNull($option->getDefault());
    }

    public function testExitCodeDryRun()
    {
        $command = new FixCommand();

        $input = $this->getInputMock(array(
            'dry-run' => true,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(0, $exitCode);
    }

    public function testExitCodeActualRun()
    {
        $fixer = $this->getFixerMock();

        $command = new FixCommand($fixer);

        $input = $this->getInputMock(array(
            'dry-run' => false,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(0, $exitCode);
    }

    public function testExitCodeDryRunWithChangedFiles()
    {
        $fixer = $this->getFixerMock(array(
            'Changed.php',
        ));

        $command = new FixCommand($fixer);

        $input = $this->getInputMock(array(
            'dry-run' => true,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(8, $exitCode);
    }

    public function testExitCodeActualRunWithChangedFiles()
    {
        $fixer = $this->getFixerMock(array(
            'Changed.php',
        ));

        $command = new FixCommand($fixer);

        $input = $this->getInputMock(array(
            'dry-run' => false,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(0, $exitCode);
    }

    public function testExitCodeDryRunWithInvalidFiles()
    {
        $errorsManager = new ErrorsManager();

        $errorsManager->report(new Error(
            Error::TYPE_INVALID,
            'Invalid.php'
        ));

        $fixer = $this->getFixerMock(array(), $errorsManager);

        $command = new FixCommand($fixer);

        $input = $this->getInputMock(array(
            'dry-run' => true,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(4, $exitCode);
    }

    public function testExitCodeActualRunWithInvalidFiles()
    {
        $errorsManager = new ErrorsManager();

        $errorsManager->report(new Error(
            Error::TYPE_INVALID,
            'Invalid.php'
        ));

        $fixer = $this->getFixerMock(array(), $errorsManager);

        $command = new FixCommand($fixer);

        $input = $this->getInputMock(array(
            'dry-run' => false,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(0, $exitCode);
    }

    public function testExitCodeDryRunWithChangedAndInvalidFiles()
    {
        $errorsManager = new ErrorsManager();

        $errorsManager->report(new Error(
            Error::TYPE_INVALID,
            'Invalid.php'
        ));

        $fixer = $this->getFixerMock(
            array(
                'Changed.php',
            ),
            $errorsManager
        );

        $command = new FixCommand($fixer);

        $input = $this->getInputMock(array(
            'dry-run' => true,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(12, $exitCode);
    }

    public function testExitCodeActualRunWithChangedAndInvalidFiles()
    {
        $errorsManager = new ErrorsManager();

        $errorsManager->report(new Error(
            Error::TYPE_INVALID,
            'Invalid.php'
        ));

        $fixer = $this->getFixerMock(
            array(
                'Changed.php',
            ),
            $errorsManager
        );

        $command = new FixCommand($fixer);

        $input = $this->getInputMock(array(
            'dry-run' => false,
        ));

        $exitCode = $command->run(
            $input,
            new NullOutput()
        );

        $this->assertSame(0, $exitCode);
    }

    /**
     * @param array $options
     *
     * @return InputInterface
     */
    private function getInputMock(array $options = array())
    {
        $input = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')->getMock();

        $arguments = array(
            'path' => __DIR__.'/../../Fixtures/FixCommand',
        );

        $input
            ->expects($this->any())
            ->method('getArgument')
            ->willReturnCallback(function ($name) use ($arguments) {
                if (array_key_exists($name, $arguments)) {
                    return $arguments[$name];
                }
            })
        ;

        $options = array_merge(
            array(
                'config-file' => __DIR__.'/../../Fixtures/FixCommand/.phpcs',
                'format' => 'txt',
            ),
            $options
        );

        $input
            ->expects($this->any())
            ->method('getOption')
            ->willReturnCallback(function ($name) use ($options) {
                if (array_key_exists($name, $options)) {
                    return $options[$name];
                }
            })
        ;

        return $input;
    }

    /**
     * @param array         $changed
     * @param ErrorsManager $errorsManager
     *
     * @return Fixer
     */
    private function getFixerMock(array $changed = array(), ErrorsManager $errorsManager = null)
    {
        $fixer = $this->getMockBuilder('Symfony\CS\Fixer')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $fixer
            ->expects($this->once())
            ->method('fix')
            ->with($this->anything())
            ->willReturn($changed)
        ;

        $fixer
            ->expects($this->any())
            ->method('getConfigs')
            ->willReturn(array())
        ;

        $fixer
            ->expects($this->any())
            ->method('getStopwatch')
            ->willReturn(new Stopwatch())
        ;

        $errorsManager = $errorsManager ?: new ErrorsManager();

        $fixer
            ->expects($this->any())
            ->method('getErrorsManager')
            ->willReturn($errorsManager)
        ;

        return $fixer;
    }
}
