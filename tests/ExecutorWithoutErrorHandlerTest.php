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

namespace PhpCsFixer\Tests;

use PhpCsFixer\ExecutorWithoutErrorHandler;
use PhpCsFixer\ExecutorWithoutErrorHandlerException;

/**
 * @internal
 *
 * @covers \PhpCsFixer\ExecutorWithoutErrorHandler
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ExecutorWithoutErrorHandlerTest extends TestCase
{
    public function testWithError(): void
    {
        $this->expectException(ExecutorWithoutErrorHandlerException::class);
        $this->expectExceptionMessageMatches('/failed to open stream: No such file or directory/i');

        ExecutorWithoutErrorHandler::execute(static fn () => fopen(__DIR__.'/404', 'r'));
    }

    public function testWithoutError(): void
    {
        self::assertTrue(
            ExecutorWithoutErrorHandler::execute(static fn () => is_readable(__DIR__))
        );
    }
}
