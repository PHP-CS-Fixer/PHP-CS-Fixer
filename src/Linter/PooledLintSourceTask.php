<?php

namespace PhpCsFixer\Linter;

use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;
use PhpCsFixer\Linter\TokenizerLinter;

class PooledLintSourceTask implements Task {
    private $source;

    /**
     * @param string $source
     */
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

