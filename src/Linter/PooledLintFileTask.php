<?php

namespace PhpCsFixer\Linter;

use Amp\Parallel\Worker\Environment;
use Amp\Parallel\Worker\Task;
use PhpCsFixer\Linter\TokenizerLinter;

class PooledLintFileTask implements Task {
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function run(Environment $environment) {
        $linter = new TokenizerLinter();

        try {
            $linter->lintFile($this->path)->check();
            return true;
        } catch( LintingException $e) {
            // its not easy to handle a exception thrown within a task on the caller site.
            // therefore we transform it to a string a re-create the exception later on.
            return $e->getMessage();
        }
    }
}
