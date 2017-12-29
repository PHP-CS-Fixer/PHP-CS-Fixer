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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->assertFileExists($readmeFile, sprintf('README file "%s" not found.', $readmeFile)); // switch to `assertFileIsReadable` on PHPUnit6
        $this->assertIsReadable($readmeFile, sprintf('Cannot read "%s".', $readmeFile));
        $this->assertTrue(is_file($readmeFile), sprintf('Expected file "%s" to be a file.', $readmeFile));
        $fileContent = file_get_contents($readmeFile);
        $this->assertInternalType('string', $fileContent, sprintf('Failed to get content of "%s"', $readmeFile));

        $app = new Application();
        $input = new ArrayInput(['readme']);

        $output = new BufferedOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        $output->setDecorated(false);

        $exitCode = $app->get('readme')->run($input, $output);
        $output = $output->fetch();
        // normalize line breaks, these are not important for the tests
        $output = str_replace(PHP_EOL, "\n", $output);

        $this->assertSame(
            0,
            $exitCode,
            sprintf("readme command did not exit successfully.\n%s", $output)
        );

        $this->assertSame(
            $output,
            $fileContent,
            'README.rst file is not up to date! Do not modify it manually! Regenerate readme with command: `php php-cs-fixer readme > README.rst`.'
        );
    }
}
