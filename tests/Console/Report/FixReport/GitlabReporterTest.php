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

use PhpCsFixer\Console\Report\FixReport\GitlabReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;
use PhpCsFixer\Tests\Test\Assert\AssertJsonSchemaTrait;

/**
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\GitlabReporter
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
        return <<<'JSON'
                        [{
                            "categories": ["Style"],
                            "check_name": "some_fixer_name_here",
                            "description": "some_fixer_name_here",
                            "fingerprint": "ad098ea6ea7a28dd85dfcdfc9e2bded0",
                            "severity": "minor",
                            "location": {
                                "path": "someFile.php",
                                "lines": {
                                    "begin": 2,
                                    "end": 7
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
        return <<<'JSON'
                        [{
                            "categories": ["Style"],
                            "check_name": "some_fixer_name_here_1",
                            "description": "some_fixer_name_here_1",
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
                            "check_name": "some_fixer_name_here_2",
                            "description": "some_fixer_name_here_2",
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
        return <<<'JSON'
                        [{
                            "categories": ["Style"],
                            "check_name": "some_fixer_name_here_1",
                            "description": "some_fixer_name_here_1",
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
                            "check_name": "some_fixer_name_here_2",
                            "description": "some_fixer_name_here_2",
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
                            "check_name": "another_fixer_name_here",
                            "description": "another_fixer_name_here",
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
