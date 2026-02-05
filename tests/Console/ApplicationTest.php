<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Console;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\WorkerCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Application
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ApplicationTest extends TestCase
{
    public function testApplication(): void
    {
        $regex = '/^PHP CS Fixer <info>\d+.\d+.\d+(-DEV)?<\/info> <info>.+<\/info>'
            .' by <comment>Fabien Potencier<\/comment>, <comment>Dariusz Ruminski<\/comment> and <comment>contributors<\/comment>\.'
            ."\nPHP runtime: <info>\\d+.\\d+.\\d+(-dev|beta\\d+)?<\\/info>$/";

        self::assertMatchesRegularExpression($regex, (new Application())->getLongVersion());
    }

    public function testGetMajorVersion(): void
    {
        self::assertSame(3, Application::getMajorVersion());
    }

    public function testWorkerExceptionsAreRenderedInMachineFriendlyWay(): void
    {
        $app = new Application();
        $app->add(new WorkerCommand(new ToolInfo()));
        $app->setAutoExit(false); // see: https://symfony.com/doc/current/console.html#testing-commands

        $appTester = new ApplicationTester($app);
        $appTester->run(['worker']);

        self::assertStringContainsString(
            WorkerCommand::ERROR_PREFIX.'{"class":"PhpCsFixer\\\Runner\\\Parallel\\\ParallelisationException","message":"Missing parallelisation options"',
            $appTester->getDisplay(),
        );
    }
}
