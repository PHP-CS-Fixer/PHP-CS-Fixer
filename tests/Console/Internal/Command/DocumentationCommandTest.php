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

namespace PhpCsFixer\Tests\Console\Internal\Command;

use PhpCsFixer\Console\Internal\Application;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Internal\Command\DocumentationCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DocumentationCommandTest extends TestCase
{
    /**
     * @large
     *
     * @todo Find out the root cause of it being slower and hitting small test time limit
     */
    public function testGeneratingDocumentation(): void
    {
        $application = new Application(
            $this->createFilesystemDouble(),
        );

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
        return new class extends Filesystem {
            public function dumpFile(string $filename, $content): void {}

            /** @phpstan-ignore-next-line */
            public function remove($files): void {}
        };
    }
}
