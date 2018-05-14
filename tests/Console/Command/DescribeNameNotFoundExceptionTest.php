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
    public function testIsInvalidArgumentException()
    {
        $exception = new DescribeNameNotFoundException(
            'Peter',
            'weird'
        );

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function testConstructorSetsValues()
    {
        $name = 'Peter';
        $type = 'weird';

        $exception = new DescribeNameNotFoundException(
            $name,
            $type
        );

        $this->assertSame($name, $exception->getName());
        $this->assertSame($type, $exception->getType());
    }
}
