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

namespace PhpCsFixer\Tests\Console\Report\ListSetsReport;

use PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory;
use PhpCsFixer\Console\Report\ListSetsReport\ReporterInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory
 */
final class ReporterFactoryTest extends TestCase
{
    public function testInterfaceIsFluent(): void
    {
        $builder = new ReporterFactory();

        $testInstance = $builder->registerBuiltInReporters();
        self::assertSame($builder, $testInstance);

        $double = $this->createReporterDouble('r1');
        $testInstance = $builder->registerReporter($double);
        self::assertSame($builder, $testInstance);
    }

    public function testRegisterBuiltInReports(): void
    {
        $builder = new ReporterFactory();

        self::assertCount(0, $builder->getFormats());

        $builder->registerBuiltInReporters();
        self::assertSame(
            ['json', 'txt'],
            $builder->getFormats()
        );
    }

    public function testThatCanRegisterAndGetReports(): void
    {
        $builder = new ReporterFactory();

        $r1 = $this->createReporterDouble('r1');
        $r2 = $this->createReporterDouble('r2');
        $r3 = $this->createReporterDouble('r3');

        $builder->registerReporter($r1);
        $builder->registerReporter($r2);
        $builder->registerReporter($r3);

        self::assertSame($r1, $builder->getReporter('r1'));
        self::assertSame($r2, $builder->getReporter('r2'));
        self::assertSame($r3, $builder->getReporter('r3'));
    }

    public function testGetFormats(): void
    {
        $builder = new ReporterFactory();

        $r1 = $this->createReporterDouble('r1');
        $r2 = $this->createReporterDouble('r2');
        $r3 = $this->createReporterDouble('r3');

        $builder->registerReporter($r1);
        $builder->registerReporter($r2);
        $builder->registerReporter($r3);

        self::assertSame(['r1', 'r2', 'r3'], $builder->getFormats());
    }

    public function testRegisterReportWithOccupiedFormat(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Reporter for format "non_unique_name" is already registered.');

        $factory = new ReporterFactory();

        $r1 = $this->createReporterDouble('non_unique_name');
        $r2 = $this->createReporterDouble('non_unique_name');
        $factory->registerReporter($r1);
        $factory->registerReporter($r2);
    }

    public function testGetNonRegisteredReport(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Reporter for format "non_registered_format" is not registered.');

        $builder = new ReporterFactory();

        $builder->getReporter('non_registered_format');
    }

    private function createReporterDouble(string $format): ReporterInterface
    {
        $reporter = $this->prophesize(ReporterInterface::class);
        $reporter->getFormat()->willReturn($format);

        return $reporter->reveal();
    }
}
