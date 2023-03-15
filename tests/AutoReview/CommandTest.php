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
 *
 * @group auto-review
 * @group covers-nothing
 */
final class CommandTest extends TestCase
{
    /**
     * @dataProvider provideCommandHasNameConstCases
     */
    public function testCommandHasNameConst(Command $command): void
    {
        static::assertNotNull($command::getDefaultName());
    }

    public static function provideCommandHasNameConstCases(): array
    {
        $application = new Application();
        $commands = $application->all();

        $names = array_filter(array_keys($commands), static function (string $name) use ($commands): bool {
            return
                // is not an alias
                !\in_array($name, $commands[$name]->getAliases(), true)
                // and is our command
                && str_starts_with(\get_class($commands[$name]), 'PhpCsFixer\\');
        });

        return array_map(static function (string $name) use ($commands): array {
            return [$commands[$name]];
        }, $names);
    }
}
