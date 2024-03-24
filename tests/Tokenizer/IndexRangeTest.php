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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\IndexRange;

/**
 * @author Michael Vorisek <https://github.com/mvorisek>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\IndexRange
 */
final class IndexRangeTest extends TestCase
{
    public function testConstructor(): void
    {
        $indexRange = new IndexRange();
        self::assertFalse((new \ReflectionProperty($indexRange, 'start'))->isInitialized($indexRange));
        self::assertFalse((new \ReflectionProperty($indexRange, 'end'))->isInitialized($indexRange));

        $indexRange = new IndexRange(5);
        self::assertTrue((new \ReflectionProperty($indexRange, 'start'))->isInitialized($indexRange));
        self::assertFalse((new \ReflectionProperty($indexRange, 'end'))->isInitialized($indexRange));
        self::assertSame(5, $indexRange->start);

        $indexRange = new IndexRange(null, 5);
        self::assertFalse((new \ReflectionProperty($indexRange, 'start'))->isInitialized($indexRange));
        self::assertTrue((new \ReflectionProperty($indexRange, 'end'))->isInitialized($indexRange));
        self::assertSame(5, $indexRange->end);

        $indexRange = new IndexRange(5, 6);
        self::assertTrue((new \ReflectionProperty($indexRange, 'start'))->isInitialized($indexRange));
        self::assertTrue((new \ReflectionProperty($indexRange, 'end'))->isInitialized($indexRange));
        self::assertSame(5, $indexRange->start);
        self::assertSame(6, $indexRange->end);
    }

    public function testCount(): void
    {
        $indexRange = new IndexRange(5, 6);
        self::assertSame(2, $indexRange->count());
    }
}
