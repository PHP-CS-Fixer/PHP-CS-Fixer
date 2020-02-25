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
use Amp\Parallel\Worker\TaskFailureException;
use Amp\Promise;
use Symfony\Component\Process\Process;

/**
 * @internal
 */
final class PooledLintingResult implements LintingResultInterface
{
    /**
     * @var string|bool|null
     */
    private $result;

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
        if (null === $this->result) {
            $this->result = \Amp\Promise\wait($this->promise);
        }

        if (is_string($this->result)) {
            throw new LintingException($this->result);
        }
    }
}
