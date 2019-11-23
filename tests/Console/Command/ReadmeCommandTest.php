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

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\ReadmeCommand;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\ReadmeCommand
 */
final class ReadmeCommandTest extends TestCase
{
    public function testIfReadmeFileIsCorrect()
    {
        $readmeFile = __DIR__.'/../../../README.rst';
        static::assertFileExists($readmeFile, sprintf('README file "%s" not found.', $readmeFile)); // switch to `assertFileIsReadable` on PHPUnit6
        static::assertIsReadable($readmeFile, sprintf('Cannot read "%s".', $readmeFile));
        static::assertTrue(is_file($readmeFile), sprintf('Expected file "%s" to be a file.', $readmeFile));
        $fileContent = file_get_contents($readmeFile);
        static::assertInternalType('string', $fileContent, sprintf('Failed to get content of "%s"', $readmeFile));

        $application = new Application();

        $commandTester = new CommandTester($application->get('readme'));

        $exitCode = $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        // normalize line breaks, these are not important for the tests
        $output = str_replace(PHP_EOL, "\n", $output);

        static::assertSame(
            0,
            $exitCode,
            sprintf("readme command did not exit successfully.\n%s", $output)
        );

        static::assertSame(
            $output,
            $fileContent,
            'README.rst file is not up to date! Do not modify it manually! Regenerate readme with command: `php php-cs-fixer readme > README.rst`.'
        );
    }

    public function testCodeNotHaveUnderlinishStyleOfHeaders()
    {
        static::assertNotRegExp(
            '/([^\s])\1{4,}/',
            file_get_contents((new \ReflectionClass(ReadmeCommand::class))->getFileName()),
            'Five same characters in row found'
        );
    }
}
