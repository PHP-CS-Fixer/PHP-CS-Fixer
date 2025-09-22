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

use PhpCsFixer\Error\Error;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Error\Error
 */
final class ErrorTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $type = 1;
        $filePath = 'foo.php';

        $error = new Error(
            $type,
            $filePath
        );

        self::assertSame($type, $error->getType());
        self::assertSame($filePath, $error->getFilePath());
        self::assertNull($error->getSource());
        self::assertSame([], $error->getAppliedFixers());
        self::assertNull($error->getDiff());
    }

    public function testConstructorSetsValues2(): void
    {
        $type = 2;
        $filePath = __FILE__;
        $source = new \Exception();
        $appliedFixers = ['some_rule'];
        $diff = '__diff__';

        $error = new Error(
            $type,
            $filePath,
            $source,
            $appliedFixers,
            $diff
        );

        self::assertSame($type, $error->getType());
        self::assertSame($filePath, $error->getFilePath());
        self::assertSame($source, $error->getSource());
        self::assertSame($appliedFixers, $error->getAppliedFixers());
        self::assertSame($diff, $error->getDiff());
    }

    public function testErrorCanBeSerialised(): void
    {
        $type = 2;
        $filePath = __FILE__;
        $source = new \Exception();
        $appliedFixers = ['some_rule'];
        $diff = '__diff__';

        $error = new Error(
            $type,
            $filePath,
            $source,
            $appliedFixers,
            $diff
        );
        $serialisedError = $error->jsonSerialize();

        self::assertSame($type, $serialisedError['type']);
        self::assertSame($filePath, $serialisedError['filePath']);
        self::assertSame($source->getMessage(), $serialisedError['source']['message']);
        self::assertSame($source->getLine(), $serialisedError['source']['line']);
        self::assertSame($source->getFile(), $serialisedError['source']['file']);
        self::assertSame($source->getCode(), $serialisedError['source']['code']);
        self::assertSame($appliedFixers, $serialisedError['appliedFixers']);
        self::assertSame($diff, $serialisedError['diff']);
    }
}
