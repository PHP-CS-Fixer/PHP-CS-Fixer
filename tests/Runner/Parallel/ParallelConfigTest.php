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

use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Runner\Parallel\ParallelConfig
 */
final class ParallelConfigTest extends TestCase
{
    /**
     * @dataProvider provideExceptionIsThrownOnNegativeValuesCases
     */
    public function testExceptionIsThrownOnNegativeValues(
        int $maxProcesses,
        int $filesPerProcess,
        int $processTimeout
    ): void {
        $this->expectException(\InvalidArgumentException::class);

        // @phpstan-ignore-next-line False-positive, we pass negative values to the constructor on purpose.
        new ParallelConfig($maxProcesses, $filesPerProcess, $processTimeout);
    }

    /**
     * @return iterable<int, array{0: int, 1: int, 2: int}>
     */
    public static function provideExceptionIsThrownOnNegativeValuesCases(): iterable
    {
        yield [-1, 1, 1];

        yield [1, -1, 1];

        yield [1, 1, -1];
    }

    public function testGettersAreReturningProperValues(): void
    {
        $config = new ParallelConfig(2, 10, 120);

        self::assertSame(2, $config->getMaxProcesses());
        self::assertSame(10, $config->getFilesPerProcess());
        self::assertSame(120, $config->getProcessTimeout());
    }
}
