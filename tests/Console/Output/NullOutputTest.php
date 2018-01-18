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

namespace PhpCsFixer\Tests\Console\Output;

use PhpCsFixer\Console\Output\NullOutput;
use PhpCsFixer\Tests\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\NullOutput
 */
final class NullOutputTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testNullOutput()
    {
        $output = new NullOutput();
        $output->printLegend();
    }
}
