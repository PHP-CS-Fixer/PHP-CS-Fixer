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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use PhpCsFixer\Runner\Runner;
use Symfony\Component\Finder\Finder;

/**
 * @BeforeMethods("setUp")
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FixersBench
{
    private Runner $runner;
    private FixerFactory $fixerFactory;

    /**
     * @var list<FixerInterface>
     */
    private array $fixers;

    /**
     * @var array<non-empty-string, FixerInterface>
     */
    private array $fixersByName;

    public function __construct()
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();

        $this->fixerFactory = $fixerFactory;

        $this->fixers = $this->fixerFactory->getFixers();
        $this->fixersByName = array_reduce(
            $this->fixers,
            static function (array $carry, FixerInterface $fixer): array {
                $carry[$fixer->getName()] = $fixer;

                return $carry;
            },
            [],
        );

        $envVarFilesLimit = filter_var(getenv('PHP_CS_FIXER_BENCH_FILES_LIMIT'), \FILTER_VALIDATE_INT, \FILTER_NULL_ON_FAILURE);

        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'src';
        $this->runner = new Runner(
            new \LimitIterator(
                Finder::create()->in($path)->getIterator(),
                0,
                null !== $envVarFilesLimit ? $envVarFilesLimit : 25,
            ),
            $this->fixers,
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
     * @param array{rule: string, config: array<string, mixed>} $params
     */
    public function setUp(array $params): void
    {
        \assert(isset($this->fixersByName[$params['rule']]));
        $fixer = $this->fixersByName[$params['rule']];

        if ($fixer instanceof ConfigurableFixerInterface) {
            $fixer->configure($params['config'] ?? []);
        }

        // manipulate internals of Runner to avoid creating new Runner for each test
        \Closure::bind(static function (Runner $runner) use ($fixer): void {
            $runner->fixers = [$fixer];
            $runner->fixersByName = [$fixer->getName() => $fixer];
        }, null, Runner::class)($this->runner);
    }

    /**
     * @param array{rule: string, config: array<string, mixed>} $params
     *
     * @ParamProviders({
     *     "provideFixerNames"
     * })
     *
     * @Revs(1)
     *
     * @Iterations(3)
     */
    public function benchSingleRule(array $params): void
    {
        $this->runner->fix();
    }

    /**
     * @return iterable<string, array{rule: string, config: array<string, mixed>}>
     */
    public function provideFixerNames(): iterable
    {
        $names = array_keys($this->fixersByName);
        sort($names);

        $ruleToLimitTo = getenv('PHP_CS_FIXER_BENCH_RULE');
        if (false !== $ruleToLimitTo && '' !== $ruleToLimitTo) {
            if (!isset($this->fixersByName[$ruleToLimitTo])) {
                throw new \Exception(\sprintf("PHP_CS_FIXER_BENCH_RULE configured to non-existing rule: '%s'.", $ruleToLimitTo));
            }
            $names = [$ruleToLimitTo];
        }

        foreach ($names as $fixerName) {
            \assert(isset($this->fixersByName[$fixerName]));
            $fixer = $this->fixersByName[$fixerName];
            $samples = $fixer->getDefinition()->getCodeSamples();

            if (0 === \count($samples)) {
                throw new \Exception(\sprintf("No code samples for '%s'.", $fixerName));
            }

            foreach ($samples as $counter => $sample) {
                yield $fixerName.'#'.$counter => ['rule' => $fixerName, 'config' => $sample->getConfiguration()];
            }
        }
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
