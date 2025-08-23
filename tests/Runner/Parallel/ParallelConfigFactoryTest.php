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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ParallelConfigFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->mockCpuCount(null);

        parent::tearDown();
    }

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
        $this->mockCpuCount(7);

        $config = ParallelConfigFactory::detect();

        self::assertSame(6, $config->getMaxProcesses());
        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config->getProcessTimeout());
    }

    public function testDetectConfigurationWithParams(): void
    {
        $this->mockCpuCount(7);

        $config1 = ParallelConfigFactory::detect(22, 2_200, 5);

        self::assertSame(5, $config1->getMaxProcesses());
        self::assertSame(22, $config1->getFilesPerProcess());
        self::assertSame(2_200, $config1->getProcessTimeout());

        $config2 = ParallelConfigFactory::detect(22, 2_200, 6);

        self::assertSame(6, $config2->getMaxProcesses());

        $config3 = ParallelConfigFactory::detect(22, 2_200, 10);

        self::assertSame(6, $config3->getMaxProcesses());
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
        $this->mockCpuCount(7);

        // First argument omitted, second one provided via named argument
        $config1 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], ['processTimeout' => 300]);

        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config1->getFilesPerProcess());
        self::assertSame(300, $config1->getProcessTimeout());

        // Flipped order of arguments using named arguments syntax
        $config2 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], [
            'maxProcesses' => 1,
            'processTimeout' => 300,
            'filesPerProcess' => 5,
        ]);

        self::assertSame(1, $config2->getMaxProcesses());
        self::assertSame(5, $config2->getFilesPerProcess());
        self::assertSame(300, $config2->getProcessTimeout());

        // Only first argument provided, but via named argument
        $config3 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], ['filesPerProcess' => 7]);

        self::assertSame(7, $config3->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config3->getProcessTimeout());

        // Only third argument provided, but via named argument
        $config3 = \call_user_func_array([ParallelConfigFactory::class, 'detect'], ['maxProcesses' => 1]);

        self::assertSame(1, $config3->getMaxProcesses());
        self::assertSame(ParallelConfig::DEFAULT_FILES_PER_PROCESS, $config3->getFilesPerProcess());
        self::assertSame(ParallelConfig::DEFAULT_PROCESS_TIMEOUT, $config3->getProcessTimeout());
    }

    /**
     * @param null|positive-int $count
     */
    private function mockCpuCount(?int $count): void
    {
        \Closure::bind(static function () use ($count): void {
            ParallelConfigFactory::$cpuDetector = null !== $count
                ? new CpuCoreCounter([
                    new DummyCpuCoreFinder($count),
                ]) : null;
        }, null, ParallelConfigFactory::class)();
    }
}
