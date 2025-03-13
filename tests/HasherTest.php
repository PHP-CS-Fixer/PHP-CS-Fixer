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
 */
final class HasherTest extends TestCase
{
    public function testCalculate(): void
    {
        $expectedHash = 'd9de49676ba2316990a5acd04c8418e8';
        // $hash = hash('xxh128', '<?php echo 1;');

        self::assertSame($expectedHash, Hasher::calculate('<?php echo 1;'));
        self::assertSame($expectedHash, Hasher::calculate('<?php echo 1;')); // calling twice, hashes should always be the same when the input doesn't change.
    }
}
