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

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\JsonReporter
 */
final class JsonReporterTest extends AbstractReporterTestCase
{
    public function createSimpleReport()
    {
        return <<<'JSON'
{
    "files": [
        {
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

    public function createWithDiffReport()
    {
        return <<<'JSON'
{
    "files": [
        {
            "name": "someFile.php",
            "diff": "this text is a diff ;)"
        }
    ],
    "time": {
        "total": 0
    },
    "memory": 0
}
JSON;
    }

    public function createWithAppliedFixersReport()
    {
        return <<<'JSON'
{
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

    public function createWithTimeAndMemoryReport()
    {
        return <<<'JSON'
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
    }

    public function createComplexReport()
    {
        return <<<'JSON'
{
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

    protected function createReporter()
    {
        return new JsonReporter();
    }

    protected function getFormat()
    {
        return 'json';
    }

    protected function createNoErrorReport()
    {
        return <<<'JSON'
{
    "files": [
    ],
    "time": {
        "total": 0
    },
    "memory": 0
}
JSON;
    }

    protected function assertFormat($expected, $input)
    {
        $this->assertJsonSchema($input);
        $this->assertJsonStringEqualsJsonString($expected, $input);
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
                    static function (array $item) { return sprintf('Property `%s`: %s.', $item['property'], $item['message']); },
                    $validator->getErrors()
                )
            )
        );
    }
}
