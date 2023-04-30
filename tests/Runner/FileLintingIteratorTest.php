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

namespace PhpCsFixer\Tests\Runner;

use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Runner\FileLintingIterator;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\FileLintingIterator
 */
final class FileLintingIteratorTest extends TestCase
{
    public function testFileLintingIteratorEmpty(): void
    {
        $fileLintingIteratorProphecy = $this->prophesize(LinterInterface::class);

        $iterator = new \ArrayIterator([]);

        $fileLintingIterator = new FileLintingIterator(
            $iterator,
            $fileLintingIteratorProphecy->reveal()
        );

        self::assertNull($fileLintingIterator->current());
        self::assertNull($fileLintingIterator->currentLintingResult());
        self::assertSame($iterator, $fileLintingIterator->getInnerIterator());
        self::assertFalse($fileLintingIterator->valid());
    }

    public function testFileLintingIterator(): void
    {
        $file = new \SplFileInfo(__FILE__);
        $fileLintingIteratorProphecy = $this->prophesize(LinterInterface::class);

        $lintingResultInterfaceProphecy = $this->prophesize(LintingResultInterface::class)->reveal();
        $fileLintingIteratorProphecy->lintFile($file)->willReturn($lintingResultInterfaceProphecy);

        $iterator = new \ArrayIterator([$file]);

        $fileLintingIterator = new FileLintingIterator(
            $iterator,
            $fileLintingIteratorProphecy->reveal()
        );

        // test when not touched current is null

        self::assertNull($fileLintingIterator->currentLintingResult());

        // test iterating

        $this->fileLintingIteratorIterationTest($fileLintingIterator, $file, $lintingResultInterfaceProphecy);

        // rewind and test again

        $fileLintingIterator->rewind();

        $this->fileLintingIteratorIterationTest($fileLintingIterator, $file, $lintingResultInterfaceProphecy);
    }

    private function fileLintingIteratorIterationTest(
        FileLintingIterator $fileLintingIterator,
        \SplFileInfo $file,
        LintingResultInterface $lintingResultInterface
    ): void {
        $iterations = 0;

        foreach ($fileLintingIterator as $lintedFile) {
            self::assertSame($file, $lintedFile);
            self::assertSame($lintingResultInterface, $fileLintingIterator->currentLintingResult());
            ++$iterations;
        }

        self::assertSame(1, $iterations);

        $fileLintingIterator->next();

        self::assertNull($fileLintingIterator->currentLintingResult());
    }
}
