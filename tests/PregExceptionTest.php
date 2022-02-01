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
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\PregException
 */
final class PregExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new PregException();

        static::assertInstanceOf(\RuntimeException::class, $exception);
    }
}
