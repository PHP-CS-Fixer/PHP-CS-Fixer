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
use PhpCsFixer\Console\Report\FixReport\JsonReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\Tests\Test\Assert\AssertJsonSchemaTrait;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\JsonReporter
 *
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class JsonReporterTest extends AbstractReporterTestCase
{
    use AssertJsonSchemaTrait;

    protected static function createSimpleReport(): string
    {
        $about = Application::getAbout();
        $diff = <<<'DIFF'
            --- Original\n+++ New\n@@ -2,7 +2,7 @@\n\n class Foo\n {\n-    public function bar($foo = 1, $bar)\n+    public function bar($foo, $bar)\n     {\n     }\n }
            DIFF;

        return <<<JSON
            {
                "about": "{$about}",
                "files": [
                    {
                        "diff": "{$diff}",
                        "name": "someFile.php"
                    }
                ],
                "time": {
                    "total": 0
                },
                "memory": 0
            }
            JSON;
    }

    protected static function createWithDiffReport(): string
    {
        $about = Application::getAbout();
        $diff = <<<'DIFF'
            --- Original\n+++ New\n@@ -2,7 +2,7 @@\n\n class Foo\n {\n-    public function bar($foo = 1, $bar)\n+    public function bar($foo, $bar)\n     {\n     }\n }
            DIFF;

        return <<<JSON
            {
                "about": "{$about}",
                "files": [
                    {
                        "name": "someFile.php",
                        "diff": "{$diff}"
                    }
                ],
                "time": {
                    "total": 0
                },
                "memory": 0
            }
            JSON;
    }

    protected static function createWithAppliedFixersReport(): string
    {
        $about = Application::getAbout();

        return <<<JSON
            {
                "about": "{$about}",
                "files": [
                    {
                        "name": "someFile.php",
                        "appliedFixers":["some_fixer_name_here_1", "some_fixer_name_here_2"]
                    }
                ],
                "time": {
                    "total": 0
                },
                "memory": 0
            }
            JSON;
    }

    protected static function createWithTimeAndMemoryReport(): string
    {
        $about = Application::getAbout();
        $diff = <<<'DIFF'
            --- Original\n+++ New\n@@ -2,7 +2,7 @@\n\n class Foo\n {\n-    public function bar($foo = 1, $bar)\n+    public function bar($foo, $bar)\n     {\n     }\n }
            DIFF;

        return <<<JSON
            {
                "about": "{$about}",
                "files": [
                    {
                        "diff": "{$diff}",
                        "name": "someFile.php"
                    }
                ],
                "memory": 2.5,
                "time": {
                    "total": 1.234
                }
            }
            JSON;
    }

    protected static function createComplexReport(): string
    {
        $about = Application::getAbout();

        return <<<JSON
            {
                "about": "{$about}",
                "files": [
                    {
                        "name": "someFile.php",
                        "appliedFixers":["some_fixer_name_here_1", "some_fixer_name_here_2"],
                        "diff": "this text is a diff ;)"
                    },
                    {
                        "name": "anotherFile.php",
                        "appliedFixers":["another_fixer_name_here"],
                        "diff": "another diff here ;)"
                    }
                ],
                "memory": 2.5,
                "time": {
                    "total": 1.234
                }
            }
            JSON;
    }

    protected function createReporter(): ReporterInterface
    {
        return new JsonReporter();
    }

    protected function getFormat(): string
    {
        return 'json';
    }

    protected static function createNoErrorReport(): string
    {
        $about = Application::getAbout();

        return <<<JSON
            {
                "about": "{$about}",
                "files": [
                ],
                "time": {
                    "total": 0
                },
                "memory": 0
            }
            JSON;
    }

    protected function assertFormat(string $expected, string $input): void
    {
        self::assertJsonSchema(__DIR__.'/../../../../doc/schemas/fix/schema.json', $input);
        self::assertJsonStringEqualsJsonString($expected, $input);
    }
}
