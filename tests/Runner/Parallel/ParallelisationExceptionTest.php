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
use PhpCsFixer\Runner\Parallel\ProcessIdentifier;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\ParallelisationException
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(ParallelisationException::class)]
final class ParallelisationExceptionTest extends TestCase
{
    public function testCreateForUnknownIdentifier(): void
    {
        $identifier = ProcessIdentifier::fromRaw('php-cs-fixer_parallel_foo');
        $exception = ParallelisationException::forUnknownIdentifier($identifier);

        self::assertSame('Unknown process identifier: php-cs-fixer_parallel_foo', $exception->getMessage());
        self::assertSame(0, $exception->getCode());
    }

    public function testCreateForUnexpectedWorkerExit(): void
    {
        $exception = ParallelisationException::forUnexpectedWorkerExit(255, null, "  PHP Fatal error: Allowed memory size exhausted\n");

        self::assertSame(
            "Worker process exited unexpectedly (exit code: 255, signal: n/a), its files were not analysed.\nWorker output:\nPHP Fatal error: Allowed memory size exhausted",
            $exception->getMessage(),
        );
    }

    public function testCreateForUnexpectedWorkerExitWithSignalAndNoOutput(): void
    {
        $exception = ParallelisationException::forUnexpectedWorkerExit(null, 9, '');

        self::assertSame(
            'Worker process exited unexpectedly (exit code: n/a, signal: 9), its files were not analysed.',
            $exception->getMessage(),
        );
    }

    public function testUnexpectedWorkerExitTruncatesLongOutput(): void
    {
        $exception = ParallelisationException::forUnexpectedWorkerExit(1, null, 'Fatal error: boom'.str_repeat('a', 5_000));

        self::assertStringContainsString("output:\nFatal error: boom", $exception->getMessage());
        self::assertStringEndsWith('...', $exception->getMessage());
        self::assertStringNotContainsString(str_repeat('a', 1_001), $exception->getMessage());
    }
}
