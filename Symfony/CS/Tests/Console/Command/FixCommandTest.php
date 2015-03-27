<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Console\Command;

use Symfony\CS\Console\Command;

/**
 * @author Andreas Möller <am@localheinz.com>
 */
class FixCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandHasCacheFileOption()
    {
        $command = new Command\FixCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('cache-file'));

        $option = $definition->getOption('cache-file');

        $this->assertNull($option->getShortcut());
        $this->assertTrue($option->isValueRequired());
        $this->assertSame('The path to the cache file', $option->getDescription());
        $this->assertNull($option->getDefault());
    }
}
