<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Report;

use PhpCsFixer\FileReader;
use PhpCsFixer\Report\RawFileOutputReporter;
use PhpCsFixer\Report\ReportSummary;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 * @covers \PhpCsFixer\Report\RawFileOutputReporter
 */
final class RawFileOutputReporterTest extends TestCase
{
    /**
     * @var RawFileOutputReporter
     */
    private $reporter;

    protected function setUp()
    {
        parent::setUp();
        $this->reporter = new RawFileOutputReporter();
    }

    public function testGetFormat()
    {
        static::assertSame(
            RawFileOutputReporter::NAME,
            $this->reporter->getFormat()
        );
    }

    /**
     * @param null|string $expectedReport
     * @param null|string $expectedException
     * @param null|string $expectedExceptionMessage
     * @param null|string $stdinContent
     *
     * @dataProvider provideGenerateCases
     */
    public function testGenerate(
        ReportSummary $reportSummary,
        $expectedReport,
        $expectedException = null,
        $expectedExceptionMessage = null,
        $stdinContent = null
    ) {
        if (null !== $stdinContent) {
            $fileReader = FileReader::createSingleton();
            (function () use ($stdinContent) {
                $this->stdinContent = $stdinContent;
            })
                ->call($fileReader)
            ;
        }

        if (null !== $expectedException) {
            $this->expectException($expectedException);
            if ($expectedExceptionMessage) {
                $this->expectExceptionMessage($expectedExceptionMessage);
            }
        }

        $actualReport = $this->reporter->generate($reportSummary);
        if (null !== $expectedReport) {
            static::assertSame($expectedReport, $actualReport);
        }
    }

    public function provideGenerateCases()
    {
        return [
            [
                new ReportSummary([], 0, 0, false, false, false, true),
                '',
            ],
            [
                new ReportSummary([], 0, 0, false, false, false, false),
                'example stdin content',
                null,
                null,
                'example stdin content',
            ],
            [
                new ReportSummary([], 0, 0, false, false, true, false),
                '\<info>example stdin content\</info>',
                null,
                null,
                '<info>example stdin content</info>',
            ],
            [
                new ReportSummary(['/some/file' => []], 0, 0, false, false, false, false),
                null,
                \RuntimeException::class,
                'The raw format is allowed only while using with stdin.',
            ],
            [
                new ReportSummary(['php://stdin' => []], 0, 0, false, false, false, false),
                null,
                \RuntimeException::class,
                'The raw format can be used only with --diff option.',
            ],
            [
                new ReportSummary(['php://stdin' => ['diff' => 'some diff']], 0, 0, false, false, false, false),
                'some diff',
            ],
            [
                new ReportSummary(['php://stdin' => ['diff' => '<some-tag>...</some-tag>']], 0, 0, false, false, true, false),
                '\<some-tag>...\</some-tag>',
            ],
        ];
    }
}
