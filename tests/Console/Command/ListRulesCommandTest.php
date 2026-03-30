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
use PhpCsFixer\Console\Command\ListRulesCommand;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\ListRulesCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ListRulesCommandTest extends TestCase
{
    public function testListWithTxtFormat(): void
    {
        $commandTester = $this->doTestExecute([
            '--format' => 'txt',
        ]);

        $resultRaw = $commandTester->getDisplay();

        $expectedResultStart = '  1) align_multiline_comment'.\PHP_EOL.'       Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.';
        self::assertStringStartsWith($expectedResultStart, $resultRaw);
        self::assertSame(0, $commandTester->getStatusCode());
    }

    public function testListWithJsonFormat(): void
    {
        $commandTester = $this->doTestExecute([
            '--format' => 'json',
        ]);

        $resultRaw = $commandTester->getDisplay();

        self::assertJson($resultRaw);
        self::assertSame(0, $commandTester->getStatusCode());
    }

    /**
     * @param array<string, bool|string> $arguments
     */
    private function doTestExecute(array $arguments): CommandTester
    {
        $application = new Application();
        $application->add(new ListRulesCommand());

        $command = $application->find('list-rules');
        $commandTester = new CommandTester($command);

        $commandTester->execute($arguments);

        return $commandTester;
    }
}
