<?php

use App\Foo;

class_exists(Foo::class);
/**
 * Constructor call to private method
 */
class Case006
{
    private $foo;
    private $bar;
    public function __construct()
    {
        $this->bar = 2;
        $this->init();
    }

    private function init()
    {
        $this->foo = new Foo();
    }
}
