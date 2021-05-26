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

use PhpCsFixer\Console\Report\ListSetsReport\ReporterInterface;
use PhpCsFixer\Console\Report\ListSetsReport\ReportSummary;
use PhpCsFixer\RuleSet\Sets\PhpCsFixerSet;
use PhpCsFixer\RuleSet\Sets\SymfonyRiskySet;
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
     * @param string $expectedReport
     *
     * @dataProvider provideGenerateCases
     */
    final public function testGenerate($expectedReport, ReportSummary $reportSummary): void
    {
        $actualReport = $this->reporter->generate($reportSummary);

        $this->assertFormat($expectedReport, $actualReport);
    }

    /**
     * @return iterable
     */
    final public function provideGenerateCases()
    {
        yield 'example' => [
            $this->createSimpleReport(),
            new ReportSummary([
                new SymfonyRiskySet(),
                new PhpCsFixerSet(),
            ]),
        ];
    }

    /**
     * @return ReporterInterface
     */
    abstract protected function createReporter();

    /**
     * @return string
     */
    abstract protected function getFormat();

    /**
     * @param string $expected
     * @param string $input
     */
    abstract protected function assertFormat($expected, $input);

    /**
     * @return string
     */
    abstract protected function createSimpleReport();
}
