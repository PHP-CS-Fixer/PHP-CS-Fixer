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

namespace PhpCsFixer\Fixer;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
interface DeprecatedFixerInterface extends FixerInterface
{
    /**
     * Returns name of the fixer to use instead.
     *
     * @return string
     */
    public function getSuccessor();
}
