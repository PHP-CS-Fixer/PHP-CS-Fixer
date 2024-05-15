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

use Fidry\CpuCoreCounter\CpuCoreCounter;
use Fidry\CpuCoreCounter\Finder\DummyCpuCoreFinder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\ParallelConfigFactory
 */
final class ParallelConfigFactoryTest extends TestCase
{
    public function testSequentialConfigHasExactlyOneProcess(): void
    {
        $config = ParallelConfigFactory::sequential();

        self::assertSame(1, $config->getMaxProcesses());
    }

    /**
     * @see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/pull/7777#discussion_r1591623367
     */
    public function testDetectConfigurationWithoutParams(): void
    {
        $parallelConfigFactoryReflection = new \ReflectionClass(ParallelConfigFactory::class);
        $cpuDetector = $parallelConfigFactoryReflection->getProperty('cpuDetector');
        $cpuDetector->setAccessible(true);
        $cpuDetector->setValue($parallelConfigFactoryReflection, new CpuCoreCounter([
            new DummyCpuCoreFinder(7),
        ]));

        $config = ParallelConfigFactory::detect();

        self::assertSame(7, $config->getMaxProcesses());
        self::assertSame(10, $config->getFilesPerProcess());
        self::assertSame(120, $config->getProcessTimeout());

        $cpuDetector->setValue($parallelConfigFactoryReflection, null);
    }

    public function testDetectConfigurationWithParams(): void
    {
        $config = ParallelConfigFactory::detect(22, 2_200);

        self::assertSame(22, $config->getFilesPerProcess());
        self::assertSame(2_200, $config->getProcessTimeout());
    }
}
