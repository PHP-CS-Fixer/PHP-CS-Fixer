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

namespace PhpCsFixer\Tests\Console\Output\Progress;

use PhpCsFixer\Console\Output\Progress\NullOutput;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\Progress\NullOutput
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NullOutputTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testNullOutput(): void
    {
        $output = new NullOutput();
        $output->printLegend();
    }
}
