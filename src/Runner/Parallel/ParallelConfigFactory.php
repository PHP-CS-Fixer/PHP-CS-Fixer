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

namespace PhpCsFixer\Runner\Parallel;

use Fidry\CpuCoreCounter\CpuCoreCounter;
use Fidry\CpuCoreCounter\Finder\DummyCpuCoreFinder;
use Fidry\CpuCoreCounter\Finder\FinderRegistry;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ParallelConfigFactory
{
    private static ?CpuCoreCounter $cpuDetector = null;

    private function __construct() {}

    public static function sequential(): ParallelConfig
    {
        return new ParallelConfig(1);
    }

    /**
     * @param null|positive-int $filesPerProcess
     * @param null|positive-int $processTimeout
     */
    public static function detect(
        ?int $filesPerProcess = null,
        ?int $processTimeout = null
    ): ParallelConfig {
        if (null === self::$cpuDetector) {
            self::$cpuDetector = new CpuCoreCounter([
                ...FinderRegistry::getDefaultLogicalFinders(),
                new DummyCpuCoreFinder(1),
            ]);
        }

        $args = array_filter(
            [
                'maxProcesses' => self::$cpuDetector->getCount(),
                'filesPerProcess' => $filesPerProcess,
                'processTimeout' => $processTimeout,
            ],
            static fn ($value): bool => null !== $value
        );

        if (\PHP_VERSION_ID < 8_00_00) {
            $args = array_values($args);
        }

        return new ParallelConfig(...$args);
    }
}
