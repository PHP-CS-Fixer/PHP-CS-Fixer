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

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Report\FixReport\GitlabReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\Console\Report\FixReport\ReportSummary;
use PhpCsFixer\Tests\Test\Assert\AssertJsonSchemaTrait;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\GitlabReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(GitlabReporter::class)]
final class GitlabReporterTest extends AbstractReporterTestCase
{
    use AssertJsonSchemaTrait;

    public function testPerFixerDiffsAreUsedWhenAvailable(): void
    {
        $fixerADiff = "--- Original\n+++ New\n@@ -27,7 +27,6 @@\n use App\\W;\n use App\\X;\n use App\\Y;\n-use App\\UnusedImport;\n use App\\Z;\n \n class Sample\n";
        $fixerBDiff = "--- Original\n+++ New\n@@ -80,5 +80,5 @@\n }\n \n function bbb()\n {\n-    return 1+1;\n+    return 1 + 1;\n }\n";

        $report = $this->reporter->generate(new ReportSummary(
            [
                'someFile.php' => [
                    'appliedFixers' => ['fixer_a', 'fixer_b'],
                    'diff' => $fixerADiff.$fixerBDiff,
                    'fixerDiffs' => [
                        'fixer_a' => $fixerADiff,
                        'fixer_b' => $fixerBDiff,
                    ],
                ],
            ],
            10,
            0,
            0,
            false,
            false,
            false,
        ));

        $entries = json_decode($report, true, 512, \JSON_THROW_ON_ERROR);
        self::assertCount(2, $entries, 'one entry per fixer at its own location');

        self::assertSame('PHP-CS-Fixer.fixer_a', $entries[0]['check_name']);
        self::assertSame(['begin' => 30, 'end' => 34], $entries[0]['location']['lines']);

        self::assertSame('PHP-CS-Fixer.fixer_b', $entries[1]['check_name']);
        self::assertSame(['begin' => 84, 'end' => 85], $entries[1]['location']['lines']);
    }

    public function testMultipleChunksInSingleFixerDiffEmitOneEntryPerChunk(): void
    {
        $multiChunkDiff = "--- Original\n+++ New\n@@ -10,3 +10,2 @@\n keep1\n-removed_a\n keep2\n@@ -50,3 +49,2 @@\n keep3\n-removed_b\n keep4\n";

        $report = $this->reporter->generate(new ReportSummary(
            [
                'someFile.php' => [
                    'appliedFixers' => ['multi_chunk_fixer'],
                    'diff' => $multiChunkDiff,
                    'fixerDiffs' => ['multi_chunk_fixer' => $multiChunkDiff],
                ],
            ],
            10,
            0,
            0,
            false,
            false,
            false,
        ));

        $entries = json_decode($report, true, 512, \JSON_THROW_ON_ERROR);
        self::assertCount(2, $entries);
        self::assertSame(['begin' => 11, 'end' => 13], $entries[0]['location']['lines']);
        self::assertSame(['begin' => 51, 'end' => 53], $entries[1]['location']['lines']);
        self::assertNotSame(
            $entries[0]['fingerprint'],
            $entries[1]['fingerprint'],
            'fingerprints must differ when same fixer reports multiple chunks',
        );
    }

    protected function createReporter(): ReporterInterface
    {
        return new GitlabReporter();
    }

    protected function getFormat(): string
    {
        return 'gitlab';
    }

    protected static function createNoErrorReport(): string
    {
        return '[]';
    }

    protected static function createSimpleReport(): string
    {
        $about = Application::getAbout();

        return <<<JSON
                        [{
                            "categories": ["Style"],
                            "check_name": "PHP-CS-Fixer.some_fixer_name_here",
                            "description": "PHP-CS-Fixer.some_fixer_name_here (custom rule)",
                            "content": {
                                "body": "{$about}\\nCheck performed with a custom rule."
                            },
                            "fingerprint": "1a745ca537fc8d1d7a4f332424bc100c",
                            "severity": "minor",
                            "location": {
                                "path": "someFile.php",
                                "lines": {
                                    "begin": 5,
                                    "end": 9
                                }
                            }
                        }]
            JSON;
    }

    protected static function createWithDiffReport(): string
    {
        return self::createSimpleReport();
    }

    protected static function createWithAppliedFixersReport(): string
    {
        $about = Application::getAbout();

        return <<<JSON
                        [{
                            "categories": ["Style"],
                            "check_name": "PHP-CS-Fixer.some_fixer_name_here_1",
                            "description": "PHP-CS-Fixer.some_fixer_name_here_1 (custom rule)",
                            "content": {
                                "body": "{$about}\\nCheck performed with a custom rule."
                            },
                            "fingerprint": "4a6d5ac462516458d5ae8718461dbe0b",
                            "severity": "minor",
                            "location": {
                                "path": "someFile.php",
                                "lines": {
                                    "begin": 0,
                                    "end": 0
                                }
                            }
                        },{
                            "categories": ["Style"],
                            "check_name": "PHP-CS-Fixer.some_fixer_name_here_2",
                            "description": "PHP-CS-Fixer.some_fixer_name_here_2 (custom rule)",
                            "content": {
                                "body": "{$about}\\nCheck performed with a custom rule."
                            },
                            "fingerprint": "41bdef686a7c3912ca9fc00894987b85",
                            "severity": "minor",
                            "location": {
                                "path": "someFile.php",
                                "lines": {
                                    "begin": 0,
                                    "end": 0
                                }
                            }
                        }]
            JSON;
    }

    protected static function createWithTimeAndMemoryReport(): string
    {
        return self::createSimpleReport();
    }

    protected static function createComplexReport(): string
    {
        $about = Application::getAbout();

        return <<<JSON
                        [{
                            "categories": ["Style"],
                            "check_name": "PHP-CS-Fixer.some_fixer_name_here_1",
                            "description": "PHP-CS-Fixer.some_fixer_name_here_1 (custom rule)",
                            "content": {
                                "body": "{$about}\\nCheck performed with a custom rule."
                            },
                            "fingerprint": "4a6d5ac462516458d5ae8718461dbe0b",
                            "severity": "minor",
                            "location": {
                                "path": "someFile.php",
                                "lines": {
                                    "begin": 0,
                                    "end": 0
                                }
                            }
                        },{
                            "categories": ["Style"],
                            "check_name": "PHP-CS-Fixer.some_fixer_name_here_2",
                            "description": "PHP-CS-Fixer.some_fixer_name_here_2 (custom rule)",
                            "content": {
                                "body": "{$about}\\nCheck performed with a custom rule."
                            },
                            "fingerprint": "41bdef686a7c3912ca9fc00894987b85",
                            "severity": "minor",
                            "location": {
                                "path": "someFile.php",
                                "lines": {
                                    "begin": 0,
                                    "end": 0
                                }
                            }
                        },{
                            "categories": ["Style"],
                            "check_name": "PHP-CS-Fixer.another_fixer_name_here",
                            "description": "PHP-CS-Fixer.another_fixer_name_here (custom rule)",
                            "content": {
                                "body": "{$about}\\nCheck performed with a custom rule."
                            },
                            "fingerprint": "f0e531c97fedfbd779cb57e610c73648",
                            "severity": "minor",
                            "location": {
                                "path": "anotherFile.php",
                                "lines": {
                                    "begin": 0,
                                    "end": 0
                                }
                            }
                        }]
            JSON;
    }

    protected function assertFormat(string $expected, string $input): void
    {
        self::assertJsonSchema(__DIR__.'/../../../../doc/schemas/fix/codeclimate.json', $input);
        self::assertJsonStringEqualsJsonString($expected, $input);
    }
}
