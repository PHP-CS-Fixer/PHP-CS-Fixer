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

namespace PhpCsFixer\Tests;

use PhpCsFixer\FixerFileProcessedEvent;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerFileProcessedEvent
 */
final class FixerFileProcessedEventTest extends TestCase
{
    public function testFixerFileProcessedEvent()
    {
        $status = FixerFileProcessedEvent::STATUS_NO_CHANGES;
        $event = new FixerFileProcessedEvent($status);

        $this->assertSame($status, $event->getStatus());
    }
}
