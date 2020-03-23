<?php

use App\Foo;

class_exists('App\Foo');
class_exists(Foo::class);
/**
 * Detect class_exists with strings
 */
class Case020
{
    private $foo;
    public function __construct()
    {
        $this->foo = new Foo();
    }
}
