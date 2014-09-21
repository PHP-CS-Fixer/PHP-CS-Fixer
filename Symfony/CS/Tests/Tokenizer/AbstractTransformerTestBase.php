<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer;

use Symfony\CS\Tokenizer\Transformers;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformerTestBase extends \PHPUnit_Framework_TestCase
{
    protected static $transformer;
    protected static $transformers;

    public static function setUpBeforeClass()
    {
        static::$transformers = static::getTransformers();
        static::$transformer = static::getTransformer();
    }

    public static function tearDownAfterClass()
    {
        static::$transformer = null;
        static::$transformers = null;
    }

    protected static function getTransformer()
    {
        $transformerClass = 'Symfony\CS\Tokenizer'.substr(get_called_class(), strlen(__NAMESPACE__), -strlen('Test'));

        $transformersReflection = new \ReflectionClass(static::$transformers);
        $propertyReflection = $transformersReflection->getProperty('items');
        $propertyReflection->setAccessible(true);

        $items = $propertyReflection->getValue(static::$transformers);

        foreach ($items as $item) {
            if ($item instanceof $transformerClass) {
                return $item;
            }
        }

        throw new \RuntimeException("Transformer $transformerClass not found.");
    }

    protected static function getTransformers()
    {
        return Transformers::create();
    }
}
