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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformator implements TransformatorInterface
{
    protected static $lastGeneratedNextCustomTokenValue = 10000;

    public function registerCustomTokens()
    {
        foreach ($this->getCustomTokenNames() as $name) {
            if (!defined($name)) {
                define($name, static::generateNextCustomTokenValue());
            }
        }
    }

    protected static function generateNextCustomTokenValue()
    {
        return ++static::$lastGeneratedNextCustomTokenValue;
    }
}
