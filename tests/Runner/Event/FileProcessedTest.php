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

namespace PhpCsFixer\Tests\Runner\Event;

use PhpCsFixer\Runner\Event\FileProcessed;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Event\FileProcessed
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FileProcessedTest extends TestCase
{
    public function testFileProcessedEvent(): void
    {
        $status = FileProcessed::STATUS_NO_CHANGES;
        $event = new FileProcessed($status);

        self::assertSame($status, $event->getStatus());
    }
}
