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
use PhpCsFixer\Tests\TestCase;
use SebastianBergmann\Diff\Differ;

/**
 * @internal
 *
 * @coversNothing
 */
final class CodeChangeTest extends TestCase
{
    public function testConstruct(): void
    {
        $codeChange = new CodeChange('a', Differ::ADDED, 99, 2);

        self::assertSame('a', $codeChange->getContent());
        self::assertSame(Differ::ADDED, $codeChange->getChange());
        self::assertSame(99, $codeChange->getNewLineNumber());
        self::assertSame(2, $codeChange->getOldLineNumber());
    }
}
