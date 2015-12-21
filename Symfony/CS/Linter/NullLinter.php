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

use React\Promise\FulfilledPromise;

/**
 * Dummy linter. No linting is performed. No error is raised.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class NullLinter implements LinterInterface
{
    /**
     * {@inheritdoc}
     */
    public function lintFile($path)
    {
        return new FulfilledPromise(true);
    }

    /**
     * {@inheritdoc}
     */
    public function lintSource($source)
    {
        return new FulfilledPromise(true);
    }
}
