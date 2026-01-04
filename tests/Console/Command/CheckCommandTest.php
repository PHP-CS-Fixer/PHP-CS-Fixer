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

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\CheckCommand;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\ToolInfo;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\CheckCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CheckCommandTest extends TestCase
{
    /**
     * This test ensures that `--dry-run` option is not available in `check` command,
     * because this command is a proxy for `fix` command which always set `--dry-run` during proxying,
     * so it does not make sense to provide this option again.
     */
    public function testDryRunModeIsUnavailable(): void
    {
        $application = new Application();
        $application->add(new CheckCommand(new ToolInfo()));

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessageMatches('/--dry-run/');

        $commandTester->execute(
            [
                'command' => $command->getName(),
                '--dry-run' => true,
            ],
        );
    }
}
