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

namespace PhpCsFixer\Tests\Benchmark;

use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Runner\Runner;
use Symfony\Component\Finder\Finder;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class TokenizerBench
{
    private Runner $runner;

    public function __construct()
    {
        $envVarFilesLimit = filter_var(getenv('PHP_CS_FIXER_BENCH_FILES_LIMIT'), \FILTER_VALIDATE_INT, \FILTER_NULL_ON_FAILURE);

        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'src';
        $this->runner = new Runner(
            new \LimitIterator(
                Finder::create()->in($path)->getIterator(),
                0,
                null !== $envVarFilesLimit ? $envVarFilesLimit : 25,
            ),
            [], // explicitly no rules to run! so process will only tokenize files and not fix them
            new NullDiffer(),
            null,
            new ErrorsManager(),
            $this->createLinterDouble(),
            true,
            new NullCacheManager(),
            new Directory($path),
            false,
            ParallelConfigFactory::sequential(),
        );
    }

    /**
     * @Revs(1)
     *
     * @Iterations(25)
     */
    public function benchTokenization(): void
    {
        $this->runner->fix();
    }

    private function createLinterDouble(): LinterInterface
    {
        return new class implements LinterInterface {
            public function isAsync(): bool
            {
                return false;
            }

            public function lintFile(string $path): LintingResultInterface
            {
                return new class implements LintingResultInterface {
                    public function check(): void {}
                };
            }

            public function lintSource(string $source): LintingResultInterface
            {
                return new class implements LintingResultInterface {
                    public function check(): void {}
                };
            }
        };
    }
}
