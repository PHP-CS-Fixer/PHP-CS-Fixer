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

namespace PhpCsFixer\Runner\Parallel;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 */
final class ParallelAction
{
    // Actions handled by the runner
    public const RUNNER_ERROR_REPORT = 'errorReport';
    public const RUNNER_HELLO = 'hello';
    public const RUNNER_RESULT = 'result';
    public const RUNNER_GET_FILE_CHUNK = 'getFileChunk';

    // Actions handled by the worker
    public const WORKER_RUN = 'run';
    public const WORKER_THANK_YOU = 'thankYou';

    private function __construct() {}
}
