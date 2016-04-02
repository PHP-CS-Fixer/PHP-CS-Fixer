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
    "files":[
        {
            "name": "someFile.php"
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                ReportSummary::create()
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                            ),
                        )
                    )
            )
        );
    }

    public function testGenerateWithDiff()
    {
        $expectedJson = <<<'JSON'
{
    "files":[
        {
            "name": "someFile.php",
            "diff": "this text is a diff ;)"
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                ReportSummary::create()
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                                'diff' => 'this text is a diff ;)',
                            ),
                        )
                    )
            )
        );
    }

    public function testGenerateWithAppliedFixers()
    {
        $expectedJson = <<<'JSON'
{
    "files":[
        {
            "name": "someFile.php",
            "appliedFixers":["some_fixer_name_here"]
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString(
            $expectedJson,
            $this->reporter->generate(
                ReportSummary::create()
                    ->setAddAppliedFixers(true)
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                            ),
                        )
                    )
            )
        );
    }

    public function testGenerateWithTimeAndMemory()
    {
        $expectedJson = <<<'JSON'
{
    "files":[
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
                ReportSummary::create()
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                            ),
                        )
                    )
                    ->setMemory(2.5 * 1024 * 1024)
                    ->setTime(1234)
            )
        );
    }

    public function testGenerateComplex()
    {
        $expectedJson = <<<'JSON'
{
    "files":[
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
                ReportSummary::create()
                    ->setAddAppliedFixers(true)
                    ->setChanged(
                        array(
                            'someFile.php' => array(
                                'appliedFixers' => array('some_fixer_name_here'),
                                'diff' => 'this text is a diff ;)',
                            ),
                            'anotherFile.php' => array(
                                'appliedFixers' => array('another_fixer_name_here'),
                                'diff' => 'another diff here ;)',
                            ),
                        )
                    )
                    ->setMemory(2.5 * 1024 * 1024)
                    ->setTime(1234)
            )
        );
    }
}
