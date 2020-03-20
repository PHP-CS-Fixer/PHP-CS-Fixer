<?php

/**
 * Test public constructor will not generate extra class_exists
 */
class Case002
{
    private $foo;
    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}
