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

use PhpCsFixer\PregException;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\PregException
 *
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class PregExceptionTest extends TestCase
{
    public function testPregException(): void
    {
        $exception = new PregException('foo', 123);

        self::assertSame('foo', $exception->getMessage());
        self::assertSame(123, $exception->getCode());
    }
}
