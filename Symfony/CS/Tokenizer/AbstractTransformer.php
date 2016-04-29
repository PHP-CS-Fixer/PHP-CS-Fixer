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

namespace Symfony\CS\Tokenizer;

use Symfony\CS\Utils;

/**
 * Abstract base for Transformer class.
 *
 * It provides unified registerCustomTokens method.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformer implements TransformerInterface
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
    public function getName()
    {
        $nameParts = explode('\\', get_called_class());
        $name = end($nameParts);

        return Utils::camelCaseToUnderscore($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

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
