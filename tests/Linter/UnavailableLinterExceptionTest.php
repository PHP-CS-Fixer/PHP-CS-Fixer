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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\UnavailableLinterException;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Linter\UnavailableLinterException
 *
 * @author Andreas Möller <am@localheinz.com>
 */
final class UnavailableLinterExceptionTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $message = 'Never heard of that one, sorry!';
        $code = 9_001;
        $previous = new \RuntimeException();

        $exception = new UnavailableLinterException(
            $message,
            $code,
            $previous
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($code, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
