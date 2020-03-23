<?php

use App\Foo;

class_exists(Foo::class);
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
