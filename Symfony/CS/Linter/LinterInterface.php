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

/**
 * Interface for PHP code linting process manager.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface LinterInterface
{
    /**
     * Lint PHP file.
     *
     * @param string $path
     *
     * @return PromiseInterface
     */
    public function lintFile($path);

    /**
     * Lint PHP code.
     *
     * @param string $source
     *
     * @return PromiseInterface
     */
    public function lintSource($source);
}
