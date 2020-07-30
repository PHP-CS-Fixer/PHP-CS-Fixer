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

namespace PhpCsFixer\Tests\Priority;

use PhpCsFixer\Priority\Priority;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Priority\Priority
 */
final class PriorityTest extends TestCase
{
    /**
     * The final relation should be:
     * first - $a
     *         $b
     *         $c1, $c2
     * last -  $d.
     */
    public function testAddingRelations()
    {
        $a = new Priority();
        $b = new Priority();
        $c1 = new Priority();
        $c2 = new Priority();
        $d = new Priority();

        $a->addLowerPriority($d);
        static::assertSame(1, $a->getPriority());
        static::assertSame(0, $d->getPriority());

        $b->addLowerPriority($d);
        static::assertSame(1, $b->getPriority());
        static::assertSame(0, $d->getPriority());

        $a->addLowerPriority($b);
        static::assertSame(2, $a->getPriority());
        static::assertSame(1, $b->getPriority());
        static::assertSame(0, $d->getPriority());

        $c1->addLowerPriority($d);
        static::assertSame(1, $c1->getPriority());
        static::assertSame(0, $d->getPriority());

        $c2->addLowerPriority($d);
        static::assertSame(1, $c2->getPriority());
        static::assertSame(0, $d->getPriority());

        $b->addLowerPriority($c1);
        static::assertSame(3, $a->getPriority());
        static::assertSame(2, $b->getPriority());
        static::assertSame(1, $c1->getPriority());
        static::assertSame(1, $c2->getPriority());
        static::assertSame(0, $d->getPriority());
    }
}
