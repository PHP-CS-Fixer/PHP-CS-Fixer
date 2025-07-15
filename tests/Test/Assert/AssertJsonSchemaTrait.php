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

namespace PhpCsFixer\Tests\Test\Assert;

use JsonSchema\Validator;

/** @internal */
trait AssertJsonSchemaTrait
{
    private static function assertJsonSchema(string $schemaFile, string $json): void
    {
        $data = json_decode($json, null, 512, \JSON_THROW_ON_ERROR);
        $validator = new Validator();
        $validator->validate($data, (object) ['$ref' => 'file://'.realpath($schemaFile)]);

        /** @var list<array{property: string, message: string}> $errors */
        $errors = $validator->getErrors();

        self::assertTrue(
            $validator->isValid(),
            implode(
                "\n",
                array_map(
                    static fn (array $item): string => \sprintf('Property `%s`: %s.', $item['property'], $item['message']),
                    $errors,
                )
            )
        );
    }
}
