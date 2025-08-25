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

namespace PhpCsFixer\Tests\Runner;

use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\RunnerConfig;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\RunnerConfig
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RunnerConfigTest extends TestCase
{
    /**
     * @dataProvider provideGettersReturnCorrectDataCases
     */
    public function testGettersReturnCorrectData(
        bool $isDryRun,
        bool $stopOnViolation,
        ParallelConfig $parallelConfig,
        ?string $configFile = null
    ): void {
        $config = new RunnerConfig($isDryRun, $stopOnViolation, $parallelConfig, $configFile);

        self::assertSame($isDryRun, $config->isDryRun());
        self::assertSame($stopOnViolation, $config->shouldStopOnViolation());
        self::assertSame($parallelConfig, $config->getParallelConfig());
        self::assertSame($configFile, $config->getConfigFile());
    }

    /**
     * @return iterable<string, array{0: bool, 1: bool, 2: ParallelConfig, 3?: null|string}>
     */
    public static function provideGettersReturnCorrectDataCases(): iterable
    {
        yield 'null config file' => [
            false,
            false,
            new ParallelConfig(1, 2, 3),
            null,
        ];

        yield 'config file provided' => [
            false,
            false,
            new ParallelConfig(1, 2, 3),
            __DIR__.'/../../../.php-cs-fixer.dist.php',
        ];
    }
}
