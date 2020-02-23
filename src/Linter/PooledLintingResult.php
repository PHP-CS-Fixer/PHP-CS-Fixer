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

    /**
     * @var Promise
     */
    private $promise;

    /**
     * @param Promise<TokenizerLintingResult> $promise
     */
    public function __construct(Promise $promise)
    {
        $this->promise = $promise;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        if (null === $this->isSuccessful) {
                /**
                 * @var TokenizerLintingResult
                 */
                $result = \Amp\Promise\wait($this->promise);
                $this->isSuccessful = $result->check();
        }

        return $this->isSuccessful;
    }
}
