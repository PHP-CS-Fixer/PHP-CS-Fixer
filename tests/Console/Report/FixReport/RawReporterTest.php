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
    private const OLD_CONTENT = "<?php \$a = (int)\$b;\n";

    private const NEW_CONTENT = "<?php \$a = (int) \$b;\n";

    protected function tearDown(): void
    {
        self::writeStdinContentOfFileReader(null);

        parent::tearDown();
    }

    public function testGetFormat(): void
    {
        self::assertSame('raw', (new RawReporter())->getFormat());
    }

    public function testGenerateForFixedFile(): void
    {
        self::writeStdinContentOfFileReader(self::OLD_CONTENT);

        self::assertSame(
            self::NEW_CONTENT,
            (new RawReporter())->generate(self::createReportSummary([
                'php://stdin' => [
                    'appliedFixers' => ['cast_spaces'],
                    'diff' => self::createDiff(),
                    'newContent' => self::NEW_CONTENT,
                ],
            ])),
        );
    }

    public function testGenerateForFileThatWasNotFixed(): void
    {
        self::writeStdinContentOfFileReader(self::NEW_CONTENT);

        self::assertSame(
            self::NEW_CONTENT,
            (new RawReporter())->generate(self::createReportSummary([])),
        );
    }

    public function testGenerateForDecoratedOutputIsNotEscaped(): void
    {
        self::writeStdinContentOfFileReader("<?php \$a = '<foo>';\n");

        self::assertSame(
            "<?php \$a = '<foo>';\n",
            (new RawReporter())->generate(self::createReportSummary([], true)),
        );
    }

    public function testGenerateThrowsForPathOtherThanStdin(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Format "raw" is available for STDIN only, got a fix result for "someFile.php".');

        (new RawReporter())->generate(self::createReportSummary([
            'someFile.php' => [
                'appliedFixers' => ['cast_spaces'],
                'diff' => self::createDiff(),
                'newContent' => self::NEW_CONTENT,
            ],
        ]));
    }

    public function testGenerateThrowsForFixResultWithoutNewContent(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Fix result for STDIN does not carry the fixed content.');

        (new RawReporter())->generate(self::createReportSummary([
            'php://stdin' => [
                'appliedFixers' => ['cast_spaces'],
                'diff' => self::createDiff(),
            ],
        ]));
    }

    private static function createDiff(): string
    {
        return <<<'DIFF'
            --- php://stdin
            +++ php://stdin
            @@ -1 +1 @@
            -<?php $a = (int)$b;
            +<?php $a = (int) $b;

            DIFF;
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
