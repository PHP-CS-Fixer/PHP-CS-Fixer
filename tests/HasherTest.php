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

use PhpCsFixer\Hasher;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Hasher
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class HasherTest extends TestCase
{
    public function testCalculate(): void
    {
        $expectedHash = \PHP_VERSION_ID >= 8_01_00 ? '6592e7f937d52d1ab4a819e9aff6c888' : 'd9de49676ba2316990a5acd04c8418e8';

        self::assertSame($expectedHash, Hasher::calculate('<?php echo 1;'));
        self::assertSame($expectedHash, Hasher::calculate('<?php echo 1;')); // calling twice, hashes should always be the same when the input doesn't change.
    }
}
