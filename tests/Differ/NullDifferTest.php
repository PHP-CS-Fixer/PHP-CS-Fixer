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

namespace PhpCsFixer\Tests\Differ;

use PhpCsFixer\Differ\NullDiffer;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Differ\NullDiffer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NullDifferTest extends AbstractDifferTestCase
{
    public function testDiffReturnsEmptyString(): void
    {
        $diff = '';

        $differ = new NullDiffer();

        self::assertSame($diff, $differ->diff($this->oldCode(), $this->newCode()));
    }
}
