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
use PhpCsFixer\Console\Command\DocumentationCommand;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\DocumentationCommand
 */
final class DocumentationCommandTest extends TestCase
{
    public function testGeneratingDocumentation(): void
    {
        $filesystem = $this->createFilesystemDouble();

        $application = new Application();
        $application->add(new DocumentationCommand($filesystem));

        $command = $application->find('documentation');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
            ],
            [
                'interactive' => false,
                'decorated' => false,
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            ],
        );

        self::assertStringContainsString(
            'Docs updated.',
            $commandTester->getDisplay(),
        );
    }

    private function createFilesystemDouble(): Filesystem
    {
        return new class() extends Filesystem {
            public function dumpFile(string $filename, $content): void {}

            /** @phpstan-ignore-next-line */
            public function remove($files): void {}
        };
    }
}
