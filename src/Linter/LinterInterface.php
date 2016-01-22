<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Linter;

use Symfony\Component\Process\Process;

/**
 * Interface for PHP code linting process manager.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface LinterInterface
{
    /**
     * Start linting process for PHP file.
     *
     * @param string $path
     *
     * @return Process|null
     */
    public function startLintingFile($path);

    /**
     * Lint PHP code.
     *
     * @param string $source
     */
    public function lintSource($source);
}
