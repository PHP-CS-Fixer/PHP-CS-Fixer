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

namespace PhpCsFixer\Tests\Console\Report\ListSetsReport;

use PhpCsFixer\Console\Report\ListSetsReport\JsonReporter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Report\ListSetsReport\JsonReporter
 */
final class JsonReporterTest extends AbstractReporterTestCase
{
    protected function createReporter()
    {
        return new JsonReporter();
    }

    protected function getFormat()
    {
        return 'json';
    }

    protected function assertFormat($expected, $input)
    {
        static::assertJsonSchema($input);
        static::assertJsonStringEqualsJsonString($expected, $input);
    }

    /**
     * @return string
     */
    protected function createSimpleReport()
    {
        return '{
    "sets": {
        "@PhpCsFixer": {
            "description": "Rule set as used by the PHP-CS-Fixer development team, highly opinionated.",
            "isRisky": false,
            "name": "@PhpCsFixer"
        },
        "@Symfony:risky": {
            "description": "Rules that follow the official `Symfony Coding Standards <https:\/\/symfony.com\/doc\/current\/contributing\/code\/standards.html>`_.",
            "isRisky": true,
            "name": "@Symfony:risky"
        }
    }
}';
    }

    /**
     * @param string $json
     */
    private static function assertJsonSchema($json)
    {
        $jsonPath = __DIR__.'/../../../../doc/schemas/list-sets/schema.json';

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
                    static function (array $item) { return sprintf('Property `%s`: %s.', $item['property'], $item['message']); },
                    $validator->getErrors()
                )
            )
        );
    }
}
