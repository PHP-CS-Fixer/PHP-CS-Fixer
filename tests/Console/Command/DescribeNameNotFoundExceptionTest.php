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

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\Console\Command\DescribeNameNotFoundException;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\DescribeNameNotFoundException
 */
final class DescribeNameNotFoundExceptionTest extends TestCase
{
    public function testIsInvalidArgumentException(): void
    {
        $exception = new DescribeNameNotFoundException(
            'Peter',
            'weird'
        );

        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function testConstructorSetsValues(): void
    {
        $name = 'Peter';
        $type = 'weird';

        $exception = new DescribeNameNotFoundException(
            $name,
            $type
        );

        self::assertSame($name, $exception->getName());
        self::assertSame($type, $exception->getType());
    }
}
