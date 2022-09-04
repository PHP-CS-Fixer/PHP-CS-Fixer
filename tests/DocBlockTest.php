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

use PhpCsFixer\DocBlock;

/**
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock
 */
final class DocBlockTest extends TestCase
{
    public function testCreating(): void
    {
        $docBlock = DocBlock::create('/** @foo */');

        static::assertCount(1, $docBlock->getTagsByName('@foo'));
        static::assertCount(0, $docBlock->getTagsByName('@bar'));
    }
}
