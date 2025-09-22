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
use PhpCsFixer\Runner\FileCachingLintingFileIterator;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\Runner\FileCachingLintingFileIterator
 */
final class FileCachingLintingFileIteratorTest extends TestCase
{
    public function testLintingEmpty(): void
    {
        $iterator = new \ArrayIterator([]);

        $fileCachingLintingIterator = new FileCachingLintingFileIterator(
            $iterator,
            $this->createLinterDouble()
        );

        self::assertNull($fileCachingLintingIterator->current());
        self::assertSame($iterator, $fileCachingLintingIterator->getInnerIterator());
        self::assertFalse($fileCachingLintingIterator->valid());
    }

    public function testLintingNonEmpty(): void
    {
        $files = [
            new \SplFileInfo(__FILE__),
            new \SplFileInfo(__FILE__),
            new \SplFileInfo(__FILE__),
        ];

        $lintingResult = new class implements LintingResultInterface {
            public function check(): void
            {
                throw new \LogicException('Not implemented.');
            }
        };

        $iterator = new \ArrayIterator($files);

        $fileCachingLintingIterator = new FileCachingLintingFileIterator(
            $iterator,
            $this->createLinterDouble($lintingResult)
        );

        self::assertLintingIteratorIteration($fileCachingLintingIterator, $lintingResult, ...$files);
        $fileCachingLintingIterator->rewind();
        self::assertLintingIteratorIteration($fileCachingLintingIterator, $lintingResult, ...$files);
    }

    private static function assertLintingIteratorIteration(
        FileCachingLintingFileIterator $fileCachingLintingIterator,
        LintingResultInterface $lintingResultInterface,
        \SplFileInfo ...$files
    ): void {
        $iterations = 0;

        foreach ($fileCachingLintingIterator as $index => $lintedFile) {
            self::assertSame($lintingResultInterface, $fileCachingLintingIterator->currentLintingResult());

            \assert(\array_key_exists($index, $files));
            self::assertSame($files[$index], $lintedFile);

            ++$iterations;
        }

        self::assertSame(\count($files), $iterations);
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
