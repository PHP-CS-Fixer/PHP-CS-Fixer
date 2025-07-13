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
use PhpCsFixer\Console\Command\ListSetsCommand;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\ListSetsCommand
 */
final class ListSetsCommandTest extends TestCase
{
    public function testListWithTxtFormat(): void
    {
        $commandTester = $this->doTestExecute([
            '--format' => 'txt',
        ]);

        $resultRaw = $commandTester->getDisplay();

        $expectedResultStart = ' 1) @DoctrineAnnotation'.\PHP_EOL.'      Rules covering Doctrine annotations';
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
        $application->add(new ListSetsCommand());

        $command = $application->find('list-sets');
        $commandTester = new CommandTester($command);

        $commandTester->execute($arguments);

        return $commandTester;
    }
}
