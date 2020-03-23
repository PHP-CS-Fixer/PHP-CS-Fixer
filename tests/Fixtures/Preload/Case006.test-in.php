<?php

use App\Foo;

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
