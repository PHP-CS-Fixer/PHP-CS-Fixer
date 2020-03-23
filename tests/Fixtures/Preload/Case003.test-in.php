<?php

use App\Foo;

/**
 * Constructor with new Foo()
 */
class Case003
{
    private $foo;
    public function __construct()
    {
        $this->foo = new Foo();
    }
}
