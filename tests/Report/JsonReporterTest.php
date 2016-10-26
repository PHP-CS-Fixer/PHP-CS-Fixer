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

use PhpCsFixer\Report\JsonReporter;
use PhpCsFixer\Report\ReportSummary;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class JsonReporterTest extends \PHPUnit_Framework_TestCase
{
    /** @var JsonReporter */
    private $reporter;

    protected function setUp()
    {
        $this->reporter = new JsonReporter();
    }

    /**
     * @covers PhpCsFixer\Report\JsonReporter::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('json', $this->reporter->getFormat());
    }

    public function testGenerateSimple()
    {
        $expectedJson = <<<'JSON'
{
    "files": [
        {
            "name": "someFile.php"
        }
    ],
    "time": {
        "total": 5
    },
    "memory": 2
}
JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                        ),
                    ),
                    5 * 1000,
                    2 * 1024 * 1024,
                    false,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateWithDiff()
    {
        $expectedJson = <<<'JSON'
{
    "files": [
        {
            "name": "someFile.php",
            "diff": "this text is a diff ;)"
        }
    ],
    "time": {
        "total": 5
    },
    "memory": 2
}
JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                            'diff' => 'this text is a diff ;)',
                        ),
                    ),
                    5 * 1000,
                    2 * 1024 * 1024,
                    false,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateWithAppliedFixers()
    {
        $expectedJson = <<<'JSON'
{
    "files": [
        {
            "name": "someFile.php",
            "appliedFixers":["some_fixer_name_here"]
        }
    ],
    "time": {
        "total": 5
    },
    "memory": 2
}
JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                        ),
                    ),
                    5 * 1000,
                    2 * 1024 * 1024,
                    true,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateWithTimeAndMemory()
    {
        $expectedJson = <<<'JSON'
{
    "files": [
        {
            "name": "someFile.php"
        }
    ],
    "memory": 2.5,
    "time": {
        "total": 1.234
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                        ),
                    ),
                    1234,
                    2.5 * 1024 * 1024,
                    false,
                    false,
                    false
                )
            )
        );
    }

    public function testGenerateComplex()
    {
        $expectedJson = <<<'JSON'
{
    "files": [
        {
            "name": "someFile.php",
            "appliedFixers":["some_fixer_name_here"],
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

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                new ReportSummary(
                    array(
                        'someFile.php' => array(
                            'appliedFixers' => array('some_fixer_name_here'),
                            'diff' => 'this text is a diff ;)',
                        ),
                        'anotherFile.php' => array(
                            'appliedFixers' => array('another_fixer_name_here'),
                            'diff' => 'another diff here ;)',
                        ),
                    ),
                    1234,
                    2.5 * 1024 * 1024,
                    true,
                    true,
                    true
                )
            )
        );
    }
}
