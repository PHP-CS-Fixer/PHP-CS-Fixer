<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface FixerInterface
{
    public function fix(\SplFileInfo $file, $content);

    public function getPriority();

    public function supports(\SplFileInfo $file);
}
