<?php

use App\Foo;

/**
 * Constructor with class reference
 */
class Case004
{
    private $foo;
    public function __construct()
    {
        $this->foo = Foo::class;
    }
}
