<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\CS\Console\Application;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ReadmeTest extends \PHPUnit_Framework_TestCase
{
    public function testIfReadmeFileIsCorrect()
    {
        $input = new ArrayInput(array('readme'));
        $output = new BufferedOutput();
        $app = new Application();

        $app->get('readme')->run($input, $output);

        $fileContent = file_get_contents(__DIR__.'/../../../README.rst');

        $this->assertSame(
            $output->fetch(),
            $fileContent,
            'README.rst file is not up to date! Do not modify it manually! Regenerate readme with command: `php php-cs-fixer readme > README.rst`.'
        );
    }
}
