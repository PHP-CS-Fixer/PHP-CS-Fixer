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

use PhpCsFixer\Runner\Event\AnalysisStarted;
use PhpCsFixer\Tests\TestCase;

/**
 * @covers \PhpCsFixer\Runner\Event\AnalysisStarted
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AnalysisStartedTest extends TestCase
{
    public function testAnalysisStartedEvent(): void
    {
        $event = new AnalysisStarted(AnalysisStarted::MODE_SEQUENTIAL, true);

        self::assertSame('sequential', $event->getMode());
        self::assertTrue($event->isDryRun());
    }
}
