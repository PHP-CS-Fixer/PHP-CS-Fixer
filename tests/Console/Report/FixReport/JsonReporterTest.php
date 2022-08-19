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

use PhpCsFixer\Console\Report\FixReport\JsonReporter;
use PhpCsFixer\Console\Report\FixReport\ReporterInterface;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\FixReport\JsonReporter
 */
final class JsonReporterTest extends AbstractReporterTestCase
{
    protected function createSimpleReport(): string
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

    protected function createWithDiffReport(): string
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

    protected function createWithAppliedFixersReport(): string
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

    protected function createWithTimeAndMemoryReport(): string
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

    protected function createComplexReport(): string
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

    protected function createReporter(): ReporterInterface
    {
        return new JsonReporter();
    }

    protected function getFormat(): string
    {
        return 'json';
    }

    protected function createNoErrorReport(): string
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

    protected function assertFormat(string $expected, string $input): void
    {
        static::assertJsonSchema($input);
        static::assertJsonStringEqualsJsonString($expected, $input);
    }

    private static function assertJsonSchema(string $json): void
    {
        $jsonPath = __DIR__.'/../../../../doc/schemas/fix/schema.json';

        $data = json_decode($json);

        $validator = new \JsonSchema\Validator();
        $validator->validate(
            $data,
            (object) ['$ref' => 'file://'.realpath($jsonPath)]
        );

        static::assertTrue(
            $validator->isValid(),
            implode(
                "\n",
                array_map(
                    static fn (array $item): string => sprintf('Property `%s`: %s.', $item['property'], $item['message']),
                    $validator->getErrors(),
                )
            )
        );
    }
}
