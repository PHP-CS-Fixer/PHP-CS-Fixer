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

namespace PhpCsFixer\Tests\Console\Report\FixReport;

use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\Console\Report\FixReport\ReportSummary;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractReporterTestCase extends TestCase
{
    /**
     * @var null|ReporterInterface
     */
    protected $reporter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reporter = $this->createReporter();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->reporter = null;
    }

    final public function testGetFormat(): void
    {
        static::assertSame(
            $this->getFormat(),
            $this->reporter->getFormat()
        );
    }

    /**
     * @dataProvider provideGenerateCases
     */
    final public function testGenerate(string $expectedReport, ReportSummary $reportSummary): void
    {
        $actualReport = $this->reporter->generate($reportSummary);

        $this->assertFormat($expectedReport, $actualReport);
    }

    final public function provideGenerateCases(): array
    {
        return [
            'no errors' => [
                $this->createNoErrorReport(),
                new ReportSummary(
                    [],
                    10,
                    0,
                    0,
                    false,
                    false,
                    false
                ),
            ],
            'simple' => [
                $this->createSimpleReport(),
                new ReportSummary(
                    [
                        'someFile.php' => [
                            'appliedFixers' => ['some_fixer_name_here'],
                            'diff' => '',
                        ],
                    ],
                    10,
                    0,
                    0,
                    false,
                    false,
                    false
                ),
            ],
            'with diff' => [
                $this->createWithDiffReport(),
                new ReportSummary(
                    [
                        'someFile.php' => [
                            'appliedFixers' => ['some_fixer_name_here'],
                            'diff' => 'this text is a diff ;)',
                        ],
                    ],
                    10,
                    0,
                    0,
                    false,
                    false,
                    false
                ),
            ],
            'with applied fixers' => [
                $this->createWithAppliedFixersReport(),
                new ReportSummary(
                    [
                        'someFile.php' => [
                            'appliedFixers' => ['some_fixer_name_here_1', 'some_fixer_name_here_2'],
                            'diff' => '',
                        ],
                    ],
                    10,
                    0,
                    0,
                    true,
                    false,
                    false
                ),
            ],
            'with time and memory' => [
                $this->createWithTimeAndMemoryReport(),
                new ReportSummary(
                    [
                        'someFile.php' => [
                            'appliedFixers' => ['some_fixer_name_here'],
                            'diff' => '',
                        ],
                    ],
                    10,
                    1234,
                    2621440, // 2.5 * 1024 * 1024
                    false,
                    false,
                    false
                ),
            ],
            'complex' => [
                $this->createComplexReport(),
                new ReportSummary(
                    [
                        'someFile.php' => [
                            'appliedFixers' => ['some_fixer_name_here_1', 'some_fixer_name_here_2'],
                            'diff' => 'this text is a diff ;)',
                        ],
                        'anotherFile.php' => [
                            'appliedFixers' => ['another_fixer_name_here'],
                            'diff' => 'another diff here ;)',
                        ],
                    ],
                    10,
                    1234,
                    2621440, // 2.5 * 1024 * 1024
                    true,
                    true,
                    true
                ),
            ],
        ];
    }

    abstract protected function createReporter(): ReporterInterface;

    abstract protected function getFormat(): string;

    abstract protected function createNoErrorReport(): string;

    abstract protected function createSimpleReport(): string;

    abstract protected function createWithDiffReport(): string;

    abstract protected function createWithAppliedFixersReport(): string;

    abstract protected function createWithTimeAndMemoryReport(): string;

    abstract protected function createComplexReport(): string;

    abstract protected function assertFormat(string $expected, string $input): void;
}
