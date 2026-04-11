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

namespace PhpCsFixer\Tests\Console\Report\ListRulesReport;

use PhpCsFixer\Console\Report\ListRulesReport\JsonReporter;
use PhpCsFixer\Console\Report\ListRulesReport\ReporterFactory;
use PhpCsFixer\Console\Report\ListRulesReport\ReporterInterface;
use PhpCsFixer\Console\Report\ListRulesReport\ReportSummary;
use PhpCsFixer\Console\Report\ListRulesReport\TextReporter;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListRulesReport\ReporterFactory
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ReporterFactoryTest extends TestCase
{
    public function testRegisterBuiltInReporters(): void
    {
        $factory = new ReporterFactory();
        $factory->registerBuiltInReporters();

        self::assertSame(['json', 'txt'], $factory->getFormats());
    }

    /**
     * @dataProvider provideGetReporterCases
     */
    public function testGetReporter(string $format, string $expectedClassName): void
    {
        $factory = new ReporterFactory();
        $factory->registerBuiltInReporters();

        $this->addToAssertionCount(1); // expected no exception
        $factory->getReporter($format);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideGetReporterCases(): iterable
    {
        yield ['json', JsonReporter::class];

        yield ['txt', TextReporter::class];
    }

    public function testGetReporterThrowsExceptionForUnknownFormat(): void
    {
        $factory = new ReporterFactory();

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The format "unknown" is not defined.');

        $factory->getReporter('unknown');
    }

    public function testRegisterReporter(): void
    {
        $factory = new ReporterFactory();

        $reporter = new class implements ReporterInterface {
            public function getFormat(): string
            {
                return 'custom';
            }

            public function generate(ReportSummary $reportSummary): string
            {
                return '';
            }
        };

        $factory->registerReporter($reporter);

        self::assertSame(['custom'], $factory->getFormats());
        self::assertSame($reporter, $factory->getReporter('custom'));
    }
}
