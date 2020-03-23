<?php

use App\Foo;

class_exists(Foo::class);
/**
 * Constructor call to public method
 */
class Case007
{
    private $foo;
    private $bar;
    public function __construct()
    {
        $this->bar = 2;
        $this->init();
    }

    public function init()
    {
        $this->foo = new Foo();
    }
}
