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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
abstract class AbstractReporterTestCase extends TestCase
{
    protected ?ReporterInterface $reporter = null;

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
        self::assertSame(
            $this->getFormat(),
            $this->reporter->getFormat(),
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

    /**
     * @return iterable<string, array{string, ReportSummary}>
     */
    final public static function provideGenerateCases(): iterable
    {
        yield 'no errors' => [
            static::createNoErrorReport(),
            new ReportSummary(
                [],
                10,
                0,
                0,
                false,
                false,
                false,
            ),
        ];

        yield 'simple' => [
            static::createSimpleReport(),
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                        'diff' => '--- Original
+++ New
@@ -2,7 +2,7 @@

 class Foo
 {
-    public function bar($foo = 1, $bar)
+    public function bar($foo, $bar)
     {
     }
 }',
                    ],
                ],
                10,
                0,
                0,
                false,
                false,
                false,
            ),
        ];

        yield 'with diff' => [
            static::createWithDiffReport(),
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                        'diff' => '--- Original
+++ New
@@ -2,7 +2,7 @@

 class Foo
 {
-    public function bar($foo = 1, $bar)
+    public function bar($foo, $bar)
     {
     }
 }',
                    ],
                ],
                10,
                0,
                0,
                false,
                false,
                false,
            ),
        ];

        yield 'with applied fixers' => [
            static::createWithAppliedFixersReport(),
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
                false,
            ),
        ];

        yield 'with time and memory' => [
            static::createWithTimeAndMemoryReport(),
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                        'diff' => '--- Original
+++ New
@@ -2,7 +2,7 @@

 class Foo
 {
-    public function bar($foo = 1, $bar)
+    public function bar($foo, $bar)
     {
     }
 }',
                    ],
                ],
                10,
                1_234,
                2_621_440, // 2.5 * 1024 * 1024
                false,
                false,
                false,
            ),
        ];

        yield 'complex' => [
            static::createComplexReport(),
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
                1_234,
                2_621_440, // 2.5 * 1024 * 1024
                true,
                true,
                true,
            ),
        ];
    }

    abstract protected function createReporter(): ReporterInterface;

    abstract protected function getFormat(): string;

    abstract protected static function createNoErrorReport(): string;

    abstract protected static function createSimpleReport(): string;

    abstract protected static function createWithDiffReport(): string;

    abstract protected static function createWithAppliedFixersReport(): string;

    abstract protected static function createWithTimeAndMemoryReport(): string;

    abstract protected static function createComplexReport(): string;

    abstract protected function assertFormat(string $expected, string $input): void;
}
