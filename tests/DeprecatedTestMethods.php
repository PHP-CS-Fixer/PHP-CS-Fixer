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

namespace PhpCsFixer\Tests;

/**
 * @internal
 *
 * @method static assertMatchesRegularExpression($pattern, $string, $message = '')
 * @method static assertFileDoesNotExist(...$args)
 * @method static assertAttributeSame($expected, $attribute, $object)
 * @method        expectExceptionMessageMatches($message)
 * @method        expectExceptionMessageRegExp($regexp)
 * @method static getStaticAttribute($object, $attribute)
 */
trait DeprecatedTestMethods
{
    /**
     * This method will only trigger under PHP <8.
     *
     * @param string $name
     * @param mixed  $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        if ('assertMatchesRegularExpression' === $name) {
            self::assertRegExp(...$arguments);

            return;
        }

        if ('assertFileDoesNotExist' === $name) {
            self::assertFileNotExist(...$arguments);
        }

        if ('assertAttributeSame' === $name) {
            self::assertAttributeSameActual(...$arguments);

            return;
        }

        if ('assertAttributeSame' === $name) {
            self::assertAttributeSameActual(...$arguments);

            return;
        }

        if ('getObjectAttribute' === $name) {
            return self::getObjectAttributeActual(...$arguments);
        }

        if ('getStaticAttribute' === $name) {
            return self::getStaticAttributeActual(...$arguments);
        }
    }

    public function __call($name, $arguments)
    {
        if ('expectExceptionMessageMatches' === $name) {
            $this->expectExceptionMessageRegExp(...$arguments);

            return;
        }
    }

    public static function getObjectAttributeActual($object, $attribute)
    {
        $reflectionProperty = new \ReflectionProperty($object, $attribute);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    public static function getStaticAttributeActual($object, $attribute)
    {
        $reflectionProperty = new \ReflectionProperty($object, $attribute);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    private static function assertAttributeSameActual($expected, $attribute, $object)
    {
        self::assertSame($expected, self::getObjectAttribute($object, $attribute));
    }
}
