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
        \Closure::bind(static function (): void {
            ParallelConfigFactory::$cpuDetector = new CpuCoreCounter([
                new DummyCpuCoreFinder(7),
            ]);
        }, null, ParallelConfigFactory::class)();

        $config = ParallelConfigFactory::detect();

        self::assertSame(7, $config->getMaxProcesses());
        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config->getProcessTimeout());

        \Closure::bind(static function (): void {
            ParallelConfigFactory::$cpuDetector = null;
        }, null, ParallelConfigFactory::class)();
    }

    public function testDetectConfigurationWithParams(): void
    {
        $config = ParallelConfigFactory::detect(22, 2_200);

        self::assertSame(22, $config->getFilesPerProcess());
        self::assertSame(2_200, $config->getProcessTimeout());
    }

    public function testDetectConfigurationWithDefaultValue(): void
    {
        $config = ParallelConfigFactory::detect(null, 60);

        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config->getFilesPerProcess());
        self::assertSame(60, $config->getProcessTimeout());
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

        // Flipped order of arguments using named arguments syntax
        $config2 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], [
            'processTimeout' => 300,
            'filesPerProcess' => 5,
        ]);

        self::assertSame(5, $config2->getFilesPerProcess());
        self::assertSame(300, $config2->getProcessTimeout());

        // Only first argument provided, but via named argument
        $config3 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], ['filesPerProcess' => 7]);

        self::assertSame(7, $config3->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config3->getProcessTimeout());
    }
}
