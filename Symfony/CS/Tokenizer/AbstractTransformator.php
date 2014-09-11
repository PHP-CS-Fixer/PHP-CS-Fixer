<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer;

use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformator implements TransformatorInterface
{
    abstract public function getConstantDefinitions();

    public function registerConstants()
    {
        foreach ($this->getConstantDefinitions() as $value => $name) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}
