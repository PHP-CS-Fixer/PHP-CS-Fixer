<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PhpCsFixer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    public function testIfReadmeFileIsCorrect()
    {
        if (!class_exists('Symfony\Component\Console\Output\BufferedOutput')) {
            $this->markTestSkipped('Unsupported symfony/console version, Symfony\Component\Console\Output\BufferedOutput was added in 2.4.');
        }

        $input = new ArrayInput(array('readme'));
        $output = new BufferedOutput();
        $app = new Application();

        $app->get('readme')->run($input, $output);

        $fileContent = file_get_contents(__DIR__.'/../README.rst');

        $this->assertSame(
            $output->fetch(),
            $fileContent,
            'README.rst file is not up to date! Do not modify it manually! Regenerate readme with command: `php php-cs-fixer readme > README.rst`.'
        );
    }
}
