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

namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;

/**
 * @TODO 4.0 Include support for this in main ConfigInterface
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
interface FilterFixerByFileAwareConfigInterface extends ConfigInterface
{
    /**
     * Registers a filter to be applied to fixers right before running them.
     * The closure can cancel the fixer by returning null.
     *
     * @param ?\Closure(FixerInterface $fixer, \SplFileInfo $file): ?FixerInterface $filterFixerByFile
     *
     * @todo v4 Introduce it in main ConfigInterface
     */
    public function setFilterFixerByFile(?\Closure $filterFixerByFile): ConfigInterface;

    /**
     * Gets the filter to be applied to fixers right before running them.
     *
     * @return ?\Closure(FixerInterface $fixer, \SplFileInfo $file): ?FixerInterface
     */
    public function getFilterFixerByFile(): ?\Closure;
}
