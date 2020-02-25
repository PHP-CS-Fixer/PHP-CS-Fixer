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

        try {
            $linter->lintSource($this->source)->check();
            return true;
        } catch( LintingException $e) {
            // its not easy to handle a exception thrown within a task on the caller site.
            // therefore we transform it to a string a re-create the exception later on.
            return $e->getMessage();
        }
    }
};

