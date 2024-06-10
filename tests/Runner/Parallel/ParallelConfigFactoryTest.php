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
use PhpCsFixer\Runner\Parallel\ParallelConfig;
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
        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config->getProcessTimeout());
        self::assertSame(ParallelConfig::DEFAULT_BUFFER_SIZE, $config->getBufferSize());

        $cpuDetector->setValue($parallelConfigFactoryReflection, null);
    }

    public function testDetectConfigurationWithParams(): void
    {
        $config = ParallelConfigFactory::detect(22, 2_200, 56_789);

        self::assertSame(22, $config->getFilesPerProcess());
        self::assertSame(2_200, $config->getProcessTimeout());
        self::assertSame(56_789, $config->getBufferSize());
    }

    public function testDetectConfigurationWithDefaultValue(): void
    {
        $config = ParallelConfigFactory::detect(null, null, 56_789);

        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config->getProcessTimeout());
        self::assertSame(56_789, $config->getBufferSize());
    }

    /**
     * @requires PHP 8.0
     */
    public function testDetectConfigurationWithNamedArgs(): void
    {
        // First argument omitted, second one provided via named argument
        $config1 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], ['processTimeout' => 300]);

        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config1->getFilesPerProcess());
        self::assertSame(300, $config1->getProcessTimeout());
        self::assertSame(ParallelConfig::DEFAULT_BUFFER_SIZE, $config1->getBufferSize());

        // Flipped order of arguments using named arguments syntax
        $config2 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], [
            'bufferSize' => 56_789,
            'processTimeout' => 300,
            'filesPerProcess' => 5,
        ]);

        self::assertSame(5, $config2->getFilesPerProcess());
        self::assertSame(300, $config2->getProcessTimeout());
        self::assertSame(56_789, $config2->getBufferSize());

        // Only first argument provided, but via named argument
        $config3 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], ['filesPerProcess' => 7]);

        self::assertSame(7, $config3->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config3->getProcessTimeout());
        self::assertSame(ParallelConfig::DEFAULT_BUFFER_SIZE, $config3->getBufferSize());
    }
}
