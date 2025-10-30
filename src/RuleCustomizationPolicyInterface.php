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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
interface RuleCustomizationPolicyInterface
{
    /**
     * Customize the given fixer for the given file.
     *
     * Return null if the fixer should not be applied to the file.
     * If you reconfigure the fixer, you should return a modified clone of it.
     */
    public function customize(FixerInterface $fixer, \SplFileInfo $file): ?FixerInterface;
}
