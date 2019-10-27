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

namespace PhpCsFixer\Tests\Differ;

use PhpCsFixer\Differ\RawDiffer;

/**
 * @internal
 * @covers \PhpCsFixer\Differ\RawDiffer
 */
final class RawDifferTest extends AbstractDifferTestCase
{
    public function testDiffReturnsDiff()
    {
        $differ = new RawDiffer();
        $oldCode = $this->oldCode();
        $newCode = $this->newCode();

        static::assertSame($newCode, $differ->diff($oldCode, $newCode));
    }
}
