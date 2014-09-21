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
 * Abstract base for Transformator class.
 *
 * It provides unified registerCustomTokens method.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformator implements TransformatorInterface
{
    /**
     * Last generated value for custom token.
     *
     * @var int
     */
    private static $lastGeneratedCustomTokenValue = 10000;

    /**
     * {@inheritdoc}
     */
    public function registerCustomTokens()
    {
        foreach ($this->getCustomTokenNames() as $name) {
            if (!defined($name)) {
                define($name, ++self::$lastGeneratedCustomTokenValue);
            }
        }
    }
}
