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

namespace PhpCsFixer\Tests\Console\Internal;

use PhpCsFixer\Console\Internal\Application;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Internal\Application
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ApplicationTest extends TestCase
{
    public function testApplication(): void
    {
        $regex = '/^PHP CS Fixer - internal <info>\d+.\d+.\d+(-DEV)?<\/info> <info>.+<\/info>'
            .' by <comment>Fabien Potencier<\/comment>, <comment>Dariusz Ruminski<\/comment> and <comment>contributors<\/comment>\.'
            ."\nPHP runtime: <info>\\d+.\\d+.\\d+(-dev|beta\\d+)?<\\/info>$/";

        self::assertMatchesRegularExpression($regex, (new Application())->getLongVersion());
    }
}
