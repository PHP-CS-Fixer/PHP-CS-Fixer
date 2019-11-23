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

namespace PhpCsFixer\Tests\Event;

use PhpCsFixer\Event\Event;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 * @covers \PhpCsFixer\Event\Event
 */
final class EventTest extends TestCase
{
    public function testInheritance()
    {
        $event = new Event();
        if (class_exists(\Symfony\Contracts\EventDispatcher\Event::class)) {
            static::assertInstanceOf(\Symfony\Contracts\EventDispatcher\Event::class, $event);
        } else {
            static::assertInstanceOf(\Symfony\Component\EventDispatcher\Event::class, $event);
        }
    }
}
