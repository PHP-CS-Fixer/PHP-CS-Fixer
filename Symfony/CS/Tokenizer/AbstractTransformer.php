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
 * Abstract base for Transformer class.
 *
 * It provides unified registerCustomTokens method.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformer implements TransformerInterface
{
    protected static function camelCaseToUnderscore($string)
    {
        return preg_replace_callback(
            '/(^|[a-z])([A-Z])/',
            function (array $matches) {
                return strtolower(strlen($matches[1]) ? $matches[1].'_'.$matches[2] : $matches[2]);
            },
            $string
        );
    }

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

        return self::camelCaseToUnderscore($name);
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
