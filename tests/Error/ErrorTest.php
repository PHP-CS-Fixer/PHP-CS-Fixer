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

namespace PhpCsFixer\Tests\Error;

use PhpCsFixer\Error\Error;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Error\Error
 */
final class ErrorTest extends TestCase
{
    public function testThatErrorTypeConstantValuesAreDifferent()
    {
        static::assertNotSame(Error::TYPE_INVALID, Error::TYPE_EXCEPTION);
        static::assertNotSame(Error::TYPE_EXCEPTION, Error::TYPE_LINT);
    }

    public function testConstructorSetsValues()
    {
        $type = 123;
        $filePath = 'foo.php';

        $error = new Error(
            $type,
            $filePath
        );

        static::assertSame($type, $error->getType());
        static::assertSame($filePath, $error->getFilePath());
        static::assertNull($error->getSource());
        static::assertSame([], $error->getAppliedFixers());
        static::assertNull($error->getDiff());
    }

    public function testConstructorSetsValues2()
    {
        $type = 456;
        $filePath = __FILE__;
        $source = __METHOD__;
        $appliedFixers = ['some_rule'];
        $diff = '__diff__';

        $error = new Error(
            $type,
            $filePath,
            $source,
            $appliedFixers,
            $diff
        );

        static::assertSame($type, $error->getType());
        static::assertSame($filePath, $error->getFilePath());
        static::assertSame($source, $error->getSource());
        static::assertSame($appliedFixers, $error->getAppliedFixers());
        static::assertSame($diff, $error->getDiff());
    }
}
