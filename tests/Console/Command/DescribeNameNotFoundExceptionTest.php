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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DescribeNameNotFoundExceptionTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $name = 'Peter';
        $type = 'weird';

        $exception = new DescribeNameNotFoundException(
            $name,
            $type,
        );

        self::assertSame($name, $exception->getName());
        self::assertSame($type, $exception->getType());
    }
}
