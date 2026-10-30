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

use PhpCsFixer\Console\Report\FixReport\GitHubReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\Console\Report\FixReport\ReportSummary;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author HypeMC <hypemc@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\GitHubReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(GitHubReporter::class)]
final class GitHubReporterTest extends AbstractReporterTestCase
{
    /**
     * For a built-in fixer the annotation message is the fixer's summary
     * (custom rules fall back to the "(custom rule)" message).
     */
    public function testGeneratedReportUsesBuiltInFixerSummaryAsMessage(): void
    {
        $fixer = new EncodingFixer();

        $reportSummary = new ReportSummary(
            [
                'someFile.php' => [
                    'appliedFixers' => [$fixer->getName()],
                    'diff' => '',
                ],
            ],
            10,
            0,
            0,
            false,
            false,
            false,
        );

        self::assertSame(
            \sprintf(
                '::error file=someFile.php,line=0,title=PHP-CS-Fixer.%s::%s',
                $fixer->getName(),
                $fixer->getDefinition()->getSummary(),
            ).\PHP_EOL,
            $this->createReporter()->generate($reportSummary),
        );
    }

    /**
     * Characters that are meaningful in the workflow command syntax must be
     * percent-encoded so they cannot break out of the `::error ...::` command.
     */
    public function testGeneratedReportEscapesWorkflowCommandCharacters(): void
    {
        $reportSummary = new ReportSummary(
            [
                "weird %:,\nname.php" => [
                    'appliedFixers' => ['some_fixer_name_here'],
                    'diff' => '',
                ],
            ],
            10,
            0,
            0,
            false,
            false,
            false,
        );

        self::assertSame(
            '::error file=weird %25%3A%2C%0Aname.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here::PHP-CS-Fixer.some_fixer_name_here (custom rule)'.\PHP_EOL,
            $this->createReporter()->generate($reportSummary),
        );
    }

    protected function createReporter(): ReporterInterface
    {
        return new GitHubReporter();
    }

    protected function getFormat(): string
    {
        return 'github';
    }

    protected static function createNoErrorReport(): string
    {
        return '';
    }

    protected static function createSimpleReport(): string
    {
        return '::error file=someFile.php,line=5,title=PHP-CS-Fixer.some_fixer_name_here::PHP-CS-Fixer.some_fixer_name_here (custom rule)'.\PHP_EOL;
    }

    protected static function createWithDiffReport(): string
    {
        return self::createSimpleReport();
    }

    protected static function createWithAppliedFixersReport(): string
    {
        return '::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_1::PHP-CS-Fixer.some_fixer_name_here_1 (custom rule)'.\PHP_EOL
            .'::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_2::PHP-CS-Fixer.some_fixer_name_here_2 (custom rule)'.\PHP_EOL;
    }

    protected static function createWithTimeAndMemoryReport(): string
    {
        return self::createSimpleReport();
    }

    protected static function createComplexReport(): string
    {
        return '::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_1::PHP-CS-Fixer.some_fixer_name_here_1 (custom rule)'.\PHP_EOL
            .'::error file=someFile.php,line=0,title=PHP-CS-Fixer.some_fixer_name_here_2::PHP-CS-Fixer.some_fixer_name_here_2 (custom rule)'.\PHP_EOL
            .'::error file=anotherFile.php,line=0,title=PHP-CS-Fixer.another_fixer_name_here::PHP-CS-Fixer.another_fixer_name_here (custom rule)'.\PHP_EOL;
    }

    protected static function createDryRunWithNoTimeReport(): string
    {
        return '';
    }

    protected function assertFormat(string $expected, string $input): void
    {
        self::assertSame($expected, $input);
    }
}
