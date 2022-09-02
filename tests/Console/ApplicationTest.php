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

namespace PhpCsFixer\Tests\Console;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Application
 */
final class ApplicationTest extends TestCase
{
    public function testApplication(): void
    {
        $regex = '/^PHP CS Fixer <info>\\d+.\\d+.\\d+(-DEV)?<\\/info> <info>.+<\\/info>'
            .' by <comment>Fabien Potencier<\\/comment> and <comment>Dariusz Ruminski<\\/comment>.'
            ."\nPHP runtime: <info>\\d+.\\d+.\\d+(-dev)?<\\/info>$/";

        static::assertMatchesRegularExpression($regex, (new Application())->getLongVersion());
    }

    public function testGetMajorVersion(): void
    {
        static::assertSame(3, Application::getMajorVersion());
    }
}
