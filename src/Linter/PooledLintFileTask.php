<?php

namespace PhpCsFixer\Linter;

use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;
use PhpCsFixer\Linter\TokenizerLinter;

class PooledLintFileTask implements Task {
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function run(Environment $environment) {
        $linter = new TokenizerLinter();
        return $linter->lintFile($this->path);
    }
}
