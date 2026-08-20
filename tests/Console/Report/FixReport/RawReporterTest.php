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

use PhpCsFixer\Console\Report\FixReport\RawReporter;
use PhpCsFixer\Console\Report\FixReport\ReportSummary;
use PhpCsFixer\FileReader;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author Dylan Pulver <dylanpulver@users.noreply.github.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\RawReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(RawReporter::class)]
final class RawReporterTest extends TestCase
{
    private ?string $originalStdinContent = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalStdinContent = self::readStdinContentOfFileReader();
    }

    protected function tearDown(): void
    {
        self::writeStdinContentOfFileReader($this->originalStdinContent);

        parent::tearDown();
    }

    public function testGetFormat(): void
    {
        self::assertSame('raw', (new RawReporter())->getFormat());
    }

    public function testGenerateForFixedFile(): void
    {
        self::writeStdinContentOfFileReader("<?php \$a = (int)\$b;\n");

        self::assertSame(
            "<?php \$a = (int) \$b;\n",
            (new RawReporter())->generate(self::createReportSummary([
                'php://stdin' => [
                    'appliedFixers' => ['cast_spaces'],
                    'diff' => '',
                    'newContent' => "<?php \$a = (int) \$b;\n",
                ],
            ])),
        );
    }

    public function testGenerateForFileThatWasNotFixed(): void
    {
        self::writeStdinContentOfFileReader("<?php \$a = (int) \$b;\n");

        self::assertSame(
            "<?php \$a = (int) \$b;\n",
            (new RawReporter())->generate(self::createReportSummary([])),
        );
    }

    public function testGenerateForFixResultWithoutContent(): void
    {
        self::writeStdinContentOfFileReader("<?php \$a = (int)\$b;\n");

        self::assertSame(
            "<?php \$a = (int)\$b;\n",
            (new RawReporter())->generate(self::createReportSummary([
                'someFile.php' => [
                    'appliedFixers' => ['cast_spaces'],
                    'diff' => '',
                ],
            ])),
        );
    }

    public function testGenerateForDecoratedOutput(): void
    {
        self::writeStdinContentOfFileReader('');

        self::assertSame(
            "\\<?php \$a = 1;\n",
            (new RawReporter())->generate(self::createReportSummary([
                'php://stdin' => [
                    'appliedFixers' => ['no_extra_blank_lines'],
                    'diff' => '',
                    'newContent' => "<?php \$a = 1;\n",
                ],
            ], true)),
        );
    }

    /**
     * @param array<string, array{appliedFixers: list<string>, diff: string, newContent?: string}> $changed
     */
    private static function createReportSummary(array $changed, bool $isDecoratedOutput = false): ReportSummary
    {
        return new ReportSummary(
            $changed,
            1,
            0,
            0,
            false,
            true,
            $isDecoratedOutput,
        );
    }

    private static function readStdinContentOfFileReader(): ?string
    {
        return \Closure::bind(
            static fn (FileReader $reader): ?string => $reader->stdinContent,
            null,
            FileReader::class,
        )(FileReader::createSingleton());
    }

    private static function writeStdinContentOfFileReader(?string $content): void
    {
        \Closure::bind(
            static function (FileReader $reader) use ($content): void {
                $reader->stdinContent = $content;
            },
            null,
            FileReader::class,
        )(FileReader::createSingleton());
    }
}
