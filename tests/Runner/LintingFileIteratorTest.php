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
use PhpCsFixer\Runner\LintingFileIterator;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\LintingFileIterator
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class LintingFileIteratorTest extends TestCase
{
    public function testFileLintingIteratorEmpty(): void
    {
        $iterator = new \ArrayIterator([]);

        $lintingFileIterator = new LintingFileIterator(
            $iterator,
            $this->createLinterDouble()
        );

        self::assertNull($lintingFileIterator->current());
        self::assertNull($lintingFileIterator->currentLintingResult());
        self::assertSame($iterator, $lintingFileIterator->getInnerIterator());
        self::assertFalse($lintingFileIterator->valid());
    }

    public function testFileLintingIterator(): void
    {
        $file = new \SplFileInfo(__FILE__);

        $lintingResult = new class implements LintingResultInterface {
            public function check(): void
            {
                throw new \LogicException('Not implemented.');
            }
        };

        $iterator = new \ArrayIterator([$file]);

        $lintingFileIterator = new LintingFileIterator(
            $iterator,
            $this->createLinterDouble($lintingResult)
        );

        // test when not touched current is null

        self::assertNull($lintingFileIterator->currentLintingResult());

        // test iterating

        $this->lintingFileIteratorIterationTest($lintingFileIterator, $file, $lintingResult);

        // rewind and test again

        $lintingFileIterator->rewind();

        $this->lintingFileIteratorIterationTest($lintingFileIterator, $file, $lintingResult);
    }

    private function lintingFileIteratorIterationTest(
        LintingFileIterator $lintingFileIterator,
        \SplFileInfo $file,
        LintingResultInterface $lintingResultInterface
    ): void {
        $iterations = 0;

        foreach ($lintingFileIterator as $lintedFile) {
            self::assertSame($file, $lintedFile);
            self::assertSame($lintingResultInterface, $lintingFileIterator->currentLintingResult());
            ++$iterations;
        }

        self::assertSame(1, $iterations);

        $lintingFileIterator->next();

        self::assertNull($lintingFileIterator->currentLintingResult());
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
