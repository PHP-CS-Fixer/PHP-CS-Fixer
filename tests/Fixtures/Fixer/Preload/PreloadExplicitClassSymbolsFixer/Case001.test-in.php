<?php

use App\Foo;

/**
 * Private constructor will run class_exists on arguments.
 */
class Case001
{
    private $foo;
    private $bar;
    private function __construct(Foo $foo, string $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
