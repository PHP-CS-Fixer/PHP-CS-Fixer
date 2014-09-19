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

use Symfony\CS\Tokenizer\Transformators;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformatorTestBase extends \PHPUnit_Framework_TestCase
{
    protected static $transformator;
    protected static $transformators;

    public static function setUpBeforeClass()
    {
        static::$transformators = static::getTransformators();
        static::$transformator = static::getTransformator();
    }

    public static function tearDownAfterClass()
    {
        static::$transformator = null;
        static::$transformators = null;
    }

    protected static function getTransformator()
    {
        $transformatorClass = 'Symfony\CS\Tokenizer'.substr(get_called_class(), strlen(__NAMESPACE__), -strlen('Test'));

        $transformatorsReflection = new \ReflectionClass(static::$transformators);
        $propertyReflection = $transformatorsReflection->getProperty('items');
        $propertyReflection->setAccessible(true);

        $items = $propertyReflection->getValue(static::$transformators);

        foreach ($items as $item) {
            if ($item instanceof $transformatorClass) {
                return $item;
            }
        }

        throw new \RuntimeException("Transformator $transformatorClass not found.");
    }

    protected static function getTransformators()
    {
        return Transformators::create();
    }
}
