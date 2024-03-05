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

namespace PhpCsFixer\Tests\FixerBlame;

use PhpCsFixer\FixerBlame\CodeChange;
use PhpCsFixer\FixerBlame\PatchInfo;
use PhpCsFixer\Tests\TestCase;
use SebastianBergmann\Diff\Differ;

/**
 * @internal
 *
 * @coversNothing
 */
final class PatchInfoTest extends TestCase
{
    public function testCountChange(): void
    {
        $patchInfo = new PatchInfo();

        self::assertSame(0, $patchInfo->getLinesAdded());
        self::assertSame(0, $patchInfo->getLinesRemoved());
        $patchInfo->countChange(Differ::ADDED);
        $patchInfo->countChange(Differ::ADDED);
        $patchInfo->countChange(Differ::ADDED);
        $patchInfo->countChange(Differ::ADDED);

        self::assertSame(4, $patchInfo->getLinesAdded());
        self::assertSame(0, $patchInfo->getLinesRemoved());

        $patchInfo->countChange(Differ::REMOVED);
        $patchInfo->countChange(Differ::REMOVED);
        $patchInfo->countChange(Differ::REMOVED);

        self::assertSame(4, $patchInfo->getLinesAdded());
        self::assertSame(3, $patchInfo->getLinesRemoved());

        self::assertSame(1, $patchInfo->getChangeSum());
    }

    public function testPatchContent(): void
    {
        $diffResult = [
            $row1 = new CodeChange('a', 1),
            $row2 = new CodeChange('b', 1),
            $row3 = new CodeChange('c', 1),
            new CodeChange('d', 1),
            new CodeChange('e', 1),
        ];

        $patchInfo = new PatchInfo();
        $patchInfo->setStartKey(1);
        $patchInfo->setEndKey(1);

        $expected = [
            $row2,
        ];

        self::assertSame($expected, $patchInfo->getPatchContent($diffResult));

        $patchInfo->setStartKey(0);
        $patchInfo->setEndKey(3);
        $expected = [
            $row1,
            $row2,
            $row3,
        ];

        self::assertSame($expected, $patchInfo->getPatchContent($diffResult));
    }
}
