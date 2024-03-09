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
 */
final class ExecutorWithoutErrorHandlerTest extends TestCase
{
    public function testWithError(): void
    {
        $this->expectException(ExecutorWithoutErrorHandlerException::class);
        $this->expectExceptionMessageMatches('/preg_match\(\): Delimiter must not be alphanumeric/');

        // @phpstan-ignore-next-line
        ExecutorWithoutErrorHandler::execute(static fn () => preg_match('bad pattern', ''));
    }

    public function testWithoutError(): void
    {
        self::assertSame(
            1,
            ExecutorWithoutErrorHandler::execute(static fn () => preg_match('/./', 'a'))
        );
    }
}
