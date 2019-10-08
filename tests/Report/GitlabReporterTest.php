<?php

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

/**
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @internal
 * @covers \PhpCsFixer\Report\GitlabReporter
 */
final class GitlabReporterTest extends AbstractReporterTestCase
{
    protected function createReporter()
    {
        return new GitlabReporter();
    }

    protected function getFormat()
    {
        return 'gitlab';
    }

    /**
     * @return string
     */
    protected function createNoErrorReport()
    {
        return '[]';
    }

    /**
     * @return string
     */
    protected function createSimpleReport()
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

    /**
     * @return string
     */
    protected function createWithDiffReport()
    {
        return $this->createSimpleReport();
    }

    /**
     * @return string
     */
    protected function createWithAppliedFixersReport()
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

    /**
     * @return string
     */
    protected function createWithTimeAndMemoryReport()
    {
        return $this->createSimpleReport();
    }

    /**
     * @return string
     */
    protected function createComplexReport()
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

    protected function assertFormat($expected, $input)
    {
        static::assertJsonStringEqualsJsonString($expected, $input);
    }
}
