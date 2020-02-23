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

namespace PhpCsFixer\Linter;

use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;

class PooledLinter implements  LinterInterface
{
    private $processPool;

    public function __construct()
    {
        $this->processPool = new DefaultPool();
    }

    public function __destruct()
    {
        yield $this->processPool->shutdown();
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        return new PooledLintingResult($this->processPool->enqueue($this->createTaskForFile($path)));
    }

    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        return new PooledLintingResult($this->processPool->enqueue($this->createTaskForSource($source)));
    }

    private function createTaskForFile($path) {
        return new class($path) implements Task {
            private $path;

            public function __construct($path)
            {
                $this->path = $path;
            }

            /**
             * @inheritDoc
             */
            public function run(Environment $environment) {
                echo "linting ". $this->path;
                $linter = new TokenizerLinter();
                return $linter->lintFile($this->path);
            }
        };
    }

    private function createTaskForSource($source) {
        return new class($source) implements Task {
            private $source;

            public function __construct($source)
            {
                $this->source = $source;
            }

            /**
             * @inheritDoc
             */
            public function run(Environment $environment) {
                $linter = new TokenizerLinter();
                return $linter->lintSource($this->source);
            }
        };
    }
}
