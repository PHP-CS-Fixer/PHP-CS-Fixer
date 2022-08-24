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

namespace PhpCsFixer\Tests\Smoke;

use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @requires OS Linux|Darwin
 *
 * @coversNothing
 *
 * @group covers-nothing
 */
abstract class AbstractSmokeTest extends TestCase
{
    protected static function markTestSkippedOrFail(string $message): void
    {
        if (getenv('PHP_CS_FIXER_TEST_ALLOW_SKIPPING_SMOKE_TESTS')) {
            static::markTestSkipped($message);
        }

        static::fail($message.' Failing as test is obligatory because of `PHP_CS_FIXER_TEST_ALLOW_SKIPPING_SMOKE_TESTS=0`.');
    }
}
