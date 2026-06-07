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
use PhpCsFixer\Console\Report\FixReport\AiReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\Tests\Test\Assert\AssertJsonSchemaTrait;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\AiReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(AiReporter::class)]
final class AiReporterTest extends AbstractReporterTestCase
{
    use AssertJsonSchemaTrait;

    protected static function createSimpleReport(): string
    {
        $about = Application::getAbout();
        $version = Application::VERSION;

        $diff = <<<'DIFF'
            --- Original\n+++ New\n@@ -2,7 +2,7 @@\n\n class Foo\n {\n-    public function bar($foo = 1, $bar)\n+    public function bar($foo, $bar)\n     {\n     }\n }
            DIFF;

        return <<<JSON
            {
                "tool": "PHP CS Fixer",
                "version": "{$version}",
                "about": "{$about}",
                "command": "fix",
                "result": "violations",
                "files_processed": 10,
                "files_with_violations_count": 1,
                "files_with_violations": [
                    {
                        "file": "someFile.php",
                        "applied_fixers": ["some_fixer_name_here"]
                    }
                ],
                "duration_s": 0,
                "memory_mb": 0
            }
            JSON;
    }

    protected static function createWithDiffReport(): string
    {
        $about = Application::getAbout();
        $version = Application::VERSION;
        $diff = <<<'DIFF'
            --- Original\n+++ New\n@@ -2,7 +2,7 @@\n\n class Foo\n {\n-    public function bar($foo = 1, $bar)\n+    public function bar($foo, $bar)\n     {\n     }\n }
            DIFF;

        return <<<JSON
            {
                "tool": "PHP CS Fixer",
                "version": "{$version}",
                "about": "{$about}",
                "command": "fix",
                "result": "violations",
                "files_processed": 10,
                "files_with_violations_count": 1,
                "files_with_violations": [
                    {
                        "file": "someFile.php",
                        "applied_fixers": ["some_fixer_name_here"]
                    }
                ],
                "duration_s": 0,
                "memory_mb": 0
            }
            JSON;
    }

    protected static function createWithAppliedFixersReport(): string
    {
        $about = Application::getAbout();
        $version = Application::VERSION;

        return <<<JSON
            {
                "tool": "PHP CS Fixer",
                "version": "{$version}",
                "about": "{$about}",
                "command": "fix",
                "result": "violations",
                "files_processed": 10,
                "files_with_violations_count": 1,
                "files_with_violations": [
                    {
                        "file": "someFile.php",
                        "applied_fixers": ["some_fixer_name_here_1", "some_fixer_name_here_2"]
                    }
                ],
                "duration_s": 0,
                "memory_mb": 0
            }
            JSON;
    }

    protected static function createWithTimeAndMemoryReport(): string
    {
        $about = Application::getAbout();
        $version = Application::VERSION;
        $diff = <<<'DIFF'
            --- Original\n+++ New\n@@ -2,7 +2,7 @@\n\n class Foo\n {\n-    public function bar($foo = 1, $bar)\n+    public function bar($foo, $bar)\n     {\n     }\n }
            DIFF;

        return <<<JSON
            {
                "tool": "PHP CS Fixer",
                "version": "{$version}",
                "about": "{$about}",
                "command": "fix",
                "result": "violations",
                "files_processed": 10,
                "files_with_violations_count": 1,
                "files_with_violations": [
                    {
                        "file": "someFile.php",
                        "applied_fixers": ["some_fixer_name_here"]
                    }
                ],
                "duration_s": 1.234,
                "memory_mb": 2.5
            }
            JSON;
    }

    protected static function createComplexReport(): string
    {
        $about = Application::getAbout();
        $version = Application::VERSION;

        return <<<JSON
            {
                "tool": "PHP CS Fixer",
                "version": "{$version}",
                "about": "{$about}",
                "command": "check",
                "result": "violations",
                "files_processed": 10,
                "files_with_violations_count": 2,
                "files_with_violations": [
                    {
                        "file": "someFile.php",
                        "applied_fixers": ["some_fixer_name_here_1", "some_fixer_name_here_2"]
                    },
                    {
                        "file": "anotherFile.php",
                        "applied_fixers": ["another_fixer_name_here"]
                    }
                ],
                "duration_s": 1.234,
                "memory_mb": 2.5
            }
            JSON;
    }

    protected static function createDryRunWithNoTimeReport(): string
    {
        $about = Application::getAbout();
        $version = Application::VERSION;

        return <<<JSON
            {
                "tool": "PHP CS Fixer",
                "version": "{$version}",
                "about": "{$about}",
                "command": "check",
                "result": "OK",
                "files_processed": 1,
                "files_with_violations_count": 0,
                "files_with_violations": [],
                "duration_s": 0,
                "memory_mb": 2.5
            }
            JSON;
    }

    protected function createReporter(): ReporterInterface
    {
        return new AiReporter();
    }

    protected function getFormat(): string
    {
        return 'ai';
    }

    protected static function createNoErrorReport(): string
    {
        $about = Application::getAbout();
        $version = Application::VERSION;

        return <<<JSON
            {
                "tool": "PHP CS Fixer",
                "version": "{$version}",
                "about": "{$about}",
                "command": "fix",
                "result": "OK",
                "files_processed": 10,
                "files_with_violations_count": 0,
                "files_with_violations": [],
                "duration_s": 0,
                "memory_mb": 0
            }
            JSON;
    }

    protected function assertFormat(string $expected, string $input): void
    {
        self::assertJsonSchema(__DIR__.'/../../../../doc/schemas/fix/ai.json', $input);
        self::assertJsonStringEqualsJsonString($expected, $input);
    }
}
