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

namespace PhpCsFixer\Tests\Console\Output;

use PhpCsFixer\Console\Output\OutputContext;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Output\OutputContext
 */
final class OutputContextTest extends TestCase
{
    public function testProvidedValuesAreAccessible(): void
    {
        $outputContext = new OutputContext(
            $output = new NullOutput(),
            $width = 100,
            $filesCount = 10
        );

        static::assertSame($output, $outputContext->getOutput());
        static::assertSame($width, $outputContext->getTerminalWidth());
        static::assertSame($filesCount, $outputContext->getFilesCount());
    }
}
