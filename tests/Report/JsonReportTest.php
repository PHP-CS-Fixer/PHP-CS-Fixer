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

use PhpCsFixer\Report\JsonReport;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class JsonReportTest extends \PHPUnit_Framework_TestCase
{
    /** @var JsonReport */
    private $report;

    protected function setUp()
    {
        $this->report = new JsonReport();
    }

    /**
     * @covers PhpCsFixer\Report\JsonReport::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('json', $this->report->getFormat());
    }

    public function testProcessSimple()
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
        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                ),
            )
        );

        $this->assertJsonStringEqualsJsonString($expectedJson, $this->report->generate());
    }

    public function testProcessWithDiff()
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
        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                    'diff' => 'this text is a diff ;)',
                ),
            )
        );

        $this->assertJsonStringEqualsJsonString($expectedJson, $this->report->generate());
    }

    public function testProcessWithAppliedFixers()
    {
        $this->report->setAddAppliedFixers(true);

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
        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                ),
            )
        );

        $this->assertJsonStringEqualsJsonString($expectedJson, $this->report->generate());
    }

    public function testProcessWithTimeAndMemory()
    {
        $this->report
            ->setTime(1234)
            ->setMemory(2.5 * 1024 * 1024);

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
        $this->report->setChanged(
            array(
                'someFile.php' => array(
                    'appliedFixers' => array('some_fixer_name_here'),
                ),
            )
        );

        $this->assertJsonStringEqualsJsonString($expectedJson, $this->report->generate());
    }

    public function testProcessComplex()
    {
        $this->report
            ->setAddAppliedFixers(true)
            ->setTime(1234)
            ->setMemory(2.5 * 1024 * 1024);

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
        $this->report->setChanged(
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
        );

        $this->assertJsonStringEqualsJsonString($expectedJson, $this->report->generate());
    }
}
