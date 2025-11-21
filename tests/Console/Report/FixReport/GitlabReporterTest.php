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
use PhpCsFixer\Tests\Test\Assert\AssertJsonSchemaTrait;

/**
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\GitlabReporter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GitlabReporterTest extends AbstractReporterTestCase
{
    use AssertJsonSchemaTrait;

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
                            "fingerprint": "ad098ea6ea7a28dd85dfcdfc9e2bded0",
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
                            "fingerprint": "b74e9385c8ae5b1f575c9c8226c7deff",
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
                            "fingerprint": "acad4672140c737a83c18d1474d84074",
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
                            "fingerprint": "b74e9385c8ae5b1f575c9c8226c7deff",
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
                            "fingerprint": "acad4672140c737a83c18d1474d84074",
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
                            "fingerprint": "30e86e533dac0f1b93bbc3a55c6908f8",
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
