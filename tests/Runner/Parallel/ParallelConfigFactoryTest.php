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

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Parallel\ParallelConfigFactory
 *
 * @TODO Test `detect()` method, but first discuss the best way to do it.
 */
final class ParallelConfigFactoryTest extends TestCase
{
    public function testSequentialConfigHasExactlyOneProcess(): void
    {
        $config = ParallelConfigFactory::sequential();

        self::assertSame(1, $config->getMaxProcesses());
    }

    public function testDetectConfigurationWithoutParams(): void
    {
        $config = ParallelConfigFactory::detect();

        self::assertSame(10, $config->getFilesPerProcess());
        self::assertSame(120, $config->getProcessTimeout());
    }

    public function testDetectConfigurationWithParams(): void
    {
        $config = ParallelConfigFactory::detect(22, 2_200);

        self::assertSame(22, $config->getFilesPerProcess());
        self::assertSame(2_200, $config->getProcessTimeout());
    }
}
