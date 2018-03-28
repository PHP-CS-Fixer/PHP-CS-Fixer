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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Command\Command;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class CommandTest extends TestCase
{
    /**
     * @dataProvider provideCommandHasNameConstCases
     *
     * @param Command $command
     */
    public function testCommandHasNameConst(Command $command)
    {
        $this->assertSame($command->getName(), constant(get_class($command).'::COMMAND_NAME'));
    }

    public function provideCommandHasNameConstCases()
    {
        $application = new Application();
        $commands = $application->all();

        $names = array_filter(array_keys($commands), function ($name) use ($commands) {
            return
                // is not an alias
                !in_array($name, $commands[$name]->getAliases(), true)
                // and is our command
                && 0 === strpos(get_class($commands[$name]), 'PhpCsFixer\\')
            ;
        });

        return array_map(function ($name) use ($commands) {
            return array($commands[$name]);
        }, $names);
    }
}
