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

use Amp\Parallel\Worker\Task;
use Amp\Promise;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
final class PooledLintingResult implements LintingResultInterface
{
    /**
     * @var bool
     */
    private $isSuccessful;

    private $promise;

    public function __construct(Promise $promise)
    {
        $this->promise = $promise;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        if (!$this->isSuccessful()) {
            // on some systems stderr is used, but on others, it's not
            throw new LintingException('error 213', 890);
            //throw new LintingException($this->process->getErrorOutput() ?: $this->process->getOutput(), $this->process->getExitCode());
        }
    }


    private function isSuccessful()
    {
        if (null === $this->isSuccessful) {
            $this->isSuccessful = yield $this->promise;
        }

        return $this->isSuccessful;
    }
}
