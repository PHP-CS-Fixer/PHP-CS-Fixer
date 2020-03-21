<?php

use App\Foo;

/**
 * Constructor call to public method with parameter
 */
class Case009
{
    private $foo;
    private $bar;
    public function __construct()
    {
        $this->bar = 2;
        $var = new Foo();
        $this->init($var);
    }

    public function init(Foo $foo)
    {
        $this->foo = $foo;
    }
}
