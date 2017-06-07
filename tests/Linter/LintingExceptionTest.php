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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\LintingException;
use PHPUnit\Framework\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\LintingException
 */
final class LintingExceptionTest extends TestCase
{
    public function testIsRuntimeException()
    {
        $exception = new LintingException();

        $this->assertInstanceOf('RuntimeException', $exception);
    }

    public function testConstructorSetsValues()
    {
        $message = 'Cannot lint this, sorry!';
        $code = 9001;
        $previous = new \RuntimeException();

        $exception = new LintingException(
            $message,
            $code,
            $previous
        );

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
