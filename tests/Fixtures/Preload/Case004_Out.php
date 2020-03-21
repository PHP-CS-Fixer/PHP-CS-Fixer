<?php

use App\Foo;

class_exists(Foo::class);
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
