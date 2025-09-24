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

namespace PhpCsFixer\Tests\Error;

use PhpCsFixer\Error\SourceExceptionFactory;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Runner\Parallel\WorkerException;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Error\SourceExceptionFactory
 */
final class SourceExceptionFactoryTest extends TestCase
{
    public function testFromArrayWithInstantiableException(): void
    {
        $exception = SourceExceptionFactory::fromArray([
            'class' => LintingException::class,
            'message' => 'foo',
            'code' => 1,
            'file' => 'foo.php',
            'line' => 1,
        ]);

        self::assertInstanceOf(LintingException::class, $exception);
        self::assertSame('foo', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
        self::assertSame('foo.php', $exception->getFile());
        self::assertSame(1, $exception->getLine());
    }

    public function testFromArrayWithInstantiableError(): void
    {
        $error = SourceExceptionFactory::fromArray([
            'class' => \ParseError::class,
            'message' => 'foo',
            'code' => 1,
            'file' => 'foo.php',
            'line' => 1,
        ]);

        self::assertInstanceOf(\ParseError::class, $error);
        self::assertSame('foo', $error->getMessage());
        self::assertSame(1, $error->getCode());
        self::assertSame('foo.php', $error->getFile());
        self::assertSame(1, $error->getLine());
    }

    public function testFromArrayWithNonInstantiableException(): void
    {
        $exception = SourceExceptionFactory::fromArray([
            'class' => WorkerException::class,
            'message' => 'foo',
            'code' => 1,
            'file' => 'foo.php',
            'line' => 1,
        ]);

        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertSame('[PhpCsFixer\Runner\Parallel\WorkerException] foo', $exception->getMessage());
        self::assertSame(1, $exception->getCode());
        self::assertSame('foo.php', $exception->getFile());
        self::assertSame(1, $exception->getLine());
    }
}
