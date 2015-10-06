<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Test;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class AccessibleObject
{
    private $object;
    private $reflection;

    public static function create($object)
    {
        return new self($object);
    }

    /**
     * @param object $object
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->reflection = new \ReflectionClass($object);
    }

    public function __call($name, array $arguments)
    {
        if (!method_exists($this->object, $name)) {
            throw new \LogicException(sprintf('Cannot call unexisting method %s->%s.', get_class($this->object), $name));
        }

        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->object, $arguments);
    }

    public function __isset($name)
    {
        try {
            $value = $this->$name;
        } catch (\LogicException $e) {
            return false;
        }

        return isset($value);
    }

    public function __get($name)
    {
        if (!property_exists($this->object, $name)) {
            throw new \LogicException(sprintf('Cannot get unexisting property %s->%s.', get_class($this->object), $name));
        }

        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    public function __set($name, $value)
    {
        if (!property_exists($this->object, $name)) {
            throw new \LogicException(sprintf('Cannot set unexisting property %s->%s = %s.', get_class($this->object), $name, var_export($value, true)));
        }

        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        $property->setValue($this->object, $value);
    }
}
