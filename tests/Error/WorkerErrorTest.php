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

use PhpCsFixer\Error\WorkerError;
use PhpCsFixer\Tests\TestCase;

/**
 * @covers \PhpCsFixer\Error\WorkerError
 *
 * @internal
 */
final class WorkerErrorTest extends TestCase
{
    public function testConstructorDataCanBeAccessed(): void
    {
        $message = 'BOOM!';
        $filePath = '/path/to/file.php';
        $line = 10;
        $code = 100;
        $trace = <<<'TRACE'
            #0 Foo
            #1 Bar
            #2 {main}
            TRACE;

        $error = new WorkerError($message, $filePath, $line, $code, $trace);

        self::assertSame($message, $error->getMessage());
        self::assertSame($filePath, $error->getFilePath());
        self::assertSame($line, $error->getLine());
        self::assertSame($code, $error->getCode());
        self::assertSame($trace, $error->getTrace());
    }
}
