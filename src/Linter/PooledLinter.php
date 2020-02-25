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
use Amp\Parallel\Worker\Pool;

class PooledLinter implements  LinterInterface
{
    /**
     * @var Pool
     */
    private $processPool;

    public function __construct(Pool $pool = null)
    {
        $this->processPool = $pool ?? \Amp\Parallel\Worker\pool();
    }

    public function __destruct()
    {
        \Amp\Promise\wait($this->processPool->shutdown());
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
        return new PooledLintFileTask($path);
    }

    private function createTaskForSource($source) {
        return new PooledLintSourceTask($source);
    }
}

