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

namespace PhpCsFixer\Tests\Report;

use PhpCsFixer\Report\GitlabReporter;
use PhpCsFixer\Report\ReporterInterface;

/**
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @internal
 * @covers \PhpCsFixer\Report\GitlabReporter
 */
final class GitlabReporterTest extends AbstractReporterTestCase
{
    protected function createReporter(): ReporterInterface
    {
        return new GitlabReporter();
    }

    protected function getFormat(): string
    {
        return 'gitlab';
    }

    protected function createNoErrorReport(): string
    {
        return '[]';
    }

    protected function createSimpleReport(): string
    {
        return <<<'JSON'
            [{
                "description": "some_fixer_name_here",
                "fingerprint": "ad098ea6ea7a28dd85dfcdfc9e2bded0",
                "location": {
                    "path": "someFile.php",
                    "lines": {
                        "begin": 0
                    }
                }
            }]
JSON;
    }

    protected function createWithDiffReport(): string
    {
        return $this->createSimpleReport();
    }

    protected function createWithAppliedFixersReport(): string
    {
        return <<<'JSON'
            [{
                "description": "some_fixer_name_here_1",
                "fingerprint": "b74e9385c8ae5b1f575c9c8226c7deff",
                "location": {
                    "path": "someFile.php",
                    "lines": {
                        "begin": 0
                    }
                }
            },{
                "description": "some_fixer_name_here_2",
                "fingerprint": "acad4672140c737a83c18d1474d84074",
                "location": {
                    "path": "someFile.php",
                    "lines": {
                        "begin": 0
                    }
                }
            }]
JSON;
    }

    protected function createWithTimeAndMemoryReport(): string
    {
        return $this->createSimpleReport();
    }

    protected function createComplexReport(): string
    {
        return <<<'JSON'
            [{
                "description": "some_fixer_name_here_1",
                "fingerprint": "b74e9385c8ae5b1f575c9c8226c7deff",
                "location": {
                    "path": "someFile.php",
                    "lines": {
                        "begin": 0
                    }
                }
            },{
                "description": "some_fixer_name_here_2",
                "fingerprint": "acad4672140c737a83c18d1474d84074",
                "location": {
                    "path": "someFile.php",
                    "lines": {
                        "begin": 0
                    }
                }
            },{
                "description": "another_fixer_name_here",
                "fingerprint": "30e86e533dac0f1b93bbc3a55c6908f8",
                "location": {
                    "path": "anotherFile.php",
                    "lines": {
                        "begin": 0
                    }
                }
            }]
JSON;
    }

    protected function assertFormat(string $expected, string $input): void
    {
        static::assertJsonStringEqualsJsonString($expected, $input);
    }
}
