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

use PhpCsFixer\Runner\Parallel\WorkerException;
use PhpCsFixer\Tests\TestCase;

/**
 * @covers \PhpCsFixer\Runner\Parallel\WorkerException
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class WorkerExceptionTest extends TestCase
{
    public function testFromRaw(): void
    {
        $exception = WorkerException::fromRaw([
            'class' => \RuntimeException::class,
            'message' => 'foo',
            'file' => 'foo.php',
            'line' => 1,
            'code' => 1,
            'trace' => '#0 bar',
        ]);

        self::assertSame('[RuntimeException] foo', $exception->getMessage());
        self::assertSame('foo.php', $exception->getFile());
        self::assertSame(1, $exception->getLine());
        self::assertSame(1, $exception->getCode());
        self::assertSame('## foo.php(1)'.\PHP_EOL.'#0 bar', $exception->getOriginalTraceAsString());
    }
}
