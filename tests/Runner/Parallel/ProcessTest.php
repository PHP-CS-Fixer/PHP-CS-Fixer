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

namespace PhpCsFixer\Tests\Runner\Parallel;

use PhpCsFixer\Runner\Parallel\ParallelisationException;
use PhpCsFixer\Runner\Parallel\Process;
use PhpCsFixer\Tests\TestCase;
use React\EventLoop\StreamSelectLoop;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\Process
 */
final class ProcessTest extends TestCase
{
    public function testRequestCantBeInvokedBeforeStart(): void
    {
        self::expectException(ParallelisationException::class);

        $process = new Process('php -v', new StreamSelectLoop(), 123);
        $process->request([]);
    }
}
