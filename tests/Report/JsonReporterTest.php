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
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\JsonReporter
 */
final class JsonReporterTest extends TestCase
{
    /** @var JsonReporter */
    private $reporter;

    protected function setUp()
    {
        $this->reporter = new JsonReporter();
    }

    /**
     * @covers \PhpCsFixer\Report\JsonReporter::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame('json', $this->reporter->getFormat());
    }

    public function testGenerateNoErrors()
    {
        $expectedReport = <<<'JSON'
{
    "files": [
    ],
    "time": {
        "total": 0
    },
    "memory": 0
}
JSON;

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [],
                0,
                0,
                false,
                false,
                false
            )
        );

        $this->assertJsonSchema($actualReport);
        $this->assertJsonStringEqualsJsonString($expectedReport, $actualReport);
    }

    public function testGenerateSimple()
    {
        $expectedReport = <<<'JSON'
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

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                    ],
                ],
                5 * 1000,
                2 * 1024 * 1024,
                false,
                false,
                false
            )
        );

        $this->assertJsonSchema($actualReport);
        $this->assertJsonStringEqualsJsonString($expectedReport, $actualReport);
    }

    public function testGenerateWithDiff()
    {
        $expectedReport = <<<'JSON'
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

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                        'diff' => 'this text is a diff ;)',
                    ],
                ],
                5 * 1000,
                2 * 1024 * 1024,
                false,
                false,
                false
            )
        );

        $this->assertJsonSchema($actualReport);
        $this->assertJsonStringEqualsJsonString($expectedReport, $actualReport);
    }

    public function testGenerateWithAppliedFixers()
    {
        $expectedReport = <<<'JSON'
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

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                    ],
                ],
                5 * 1000,
                2 * 1024 * 1024,
                true,
                false,
                false
            )
        );

        $this->assertJsonSchema($actualReport);
        $this->assertJsonStringEqualsJsonString($expectedReport, $actualReport);
    }

    public function testGenerateWithTimeAndMemory()
    {
        $expectedReport = <<<'JSON'
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

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                    ],
                ],
                1234,
                2.5 * 1024 * 1024,
                false,
                false,
                false
            )
        );

        $this->assertJsonSchema($actualReport);
        $this->assertJsonStringEqualsJsonString($expectedReport, $actualReport);
    }

    public function testGenerateComplex()
    {
        $expectedReport = <<<'JSON'
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

        $actualReport = $this->reporter->generate(
            new ReportSummary(
                [
                    'someFile.php' => [
                        'appliedFixers' => ['some_fixer_name_here'],
                        'diff' => 'this text is a diff ;)',
                    ],
                    'anotherFile.php' => [
                        'appliedFixers' => ['another_fixer_name_here'],
                        'diff' => 'another diff here ;)',
                    ],
                ],
                1234,
                2.5 * 1024 * 1024,
                true,
                true,
                true
            )
        );

        $this->assertJsonSchema($actualReport);
        $this->assertJsonStringEqualsJsonString($expectedReport, $actualReport);
    }

    /**
     * @param string $json
     */
    private function assertJsonSchema($json)
    {
        $jsonPath = __DIR__.'/../../doc/schema.json';

        $data = json_decode($json);

        $validator = new \JsonSchema\Validator();
        $validator->validate(
            $data,
            (object) ['$ref' => 'file://'.realpath($jsonPath)]
        );

        $this->assertTrue(
            $validator->isValid(),
            implode(
                "\n",
                array_map(
                    function (array $item) { return sprintf('Property `%s`: %s.', $item['property'], $item['message']); },
                    $validator->getErrors()
                )
            )
        );
    }
}
