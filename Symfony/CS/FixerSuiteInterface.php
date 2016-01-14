<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Pierre Plazanet
 */
interface FixerSuiteInterface
{
    /**
     * Returns all fixers contained in this suite.
     *
     * @return FixerInterface[]
     */
    public function getFixers();
}
