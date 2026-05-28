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

namespace PhpCsFixer\Tests\Console\Output\Progress;

use PhpCsFixer\Console\Output\Progress\ProgressOutputType;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\Progress\ProgressOutputType
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(ProgressOutputType::class)]
final class ProgressOutputTypeTest extends TestCase
{
    public function testAll(): void
    {
        $types = array_values((new \ReflectionClass(ProgressOutputType::class))->getConstants());
        sort($types);

        self::assertSame($types, ProgressOutputType::all());
    }
}
