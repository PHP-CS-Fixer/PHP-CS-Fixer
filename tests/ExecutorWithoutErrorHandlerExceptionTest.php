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

use PhpCsFixer\ExecutorWithoutErrorHandlerException;

/**
 * @internal
 *
 * @covers \PhpCsFixer\ExecutorWithoutErrorHandlerException
 */
final class ExecutorWithoutErrorHandlerExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new ExecutorWithoutErrorHandlerException('foo', 123);

        self::assertSame('foo', $exception->getMessage());
        self::assertSame(123, $exception->getCode());
    }
}
