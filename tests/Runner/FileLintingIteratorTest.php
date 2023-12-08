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
        $iterator = new \ArrayIterator([]);

        $fileLintingIterator = new FileLintingIterator(
            $iterator,
            $this->createLinterDouble()
        );

        self::assertNull($fileLintingIterator->current());
        self::assertNull($fileLintingIterator->currentLintingResult());
        self::assertSame($iterator, $fileLintingIterator->getInnerIterator());
        self::assertFalse($fileLintingIterator->valid());
    }

    public function testFileLintingIterator(): void
    {
        $file = new \SplFileInfo(__FILE__);

        $lintingResult = new class() implements LintingResultInterface {
            public function check(): void
            {
                throw new \LogicException('Not implemented.');
            }
        };

        $iterator = new \ArrayIterator([$file]);

        $fileLintingIterator = new FileLintingIterator(
            $iterator,
            $this->createLinterDouble($lintingResult)
        );

        // test when not touched current is null

        self::assertNull($fileLintingIterator->currentLintingResult());

        // test iterating

        $this->fileLintingIteratorIterationTest($fileLintingIterator, $file, $lintingResult);

        // rewind and test again

        $fileLintingIterator->rewind();

        $this->fileLintingIteratorIterationTest($fileLintingIterator, $file, $lintingResult);
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

    private function createLinterDouble(?LintingResultInterface $lintingResult = null): LinterInterface
    {
        return new class($lintingResult) implements LinterInterface {
            private ?LintingResultInterface $lintingResult;

            public function __construct(?LintingResultInterface $lintingResult)
            {
                $this->lintingResult = $lintingResult;
            }

            public function isAsync(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function lintFile(string $path): LintingResultInterface
            {
                return $this->lintingResult;
            }

            public function lintSource(string $source): LintingResultInterface
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
