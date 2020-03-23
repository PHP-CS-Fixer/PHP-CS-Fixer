<?php

use App\Foo;

class_exists(Foo::class);
/**
 * Constructor call to private method with parameter
 */
class Case008
{
    private $foo;
    private $bar;
    public function __construct()
    {
        $this->bar = 2;
        $var = new Foo();
        $this->init($var);
    }

    private function init(Foo $foo)
    {
        $this->foo = $foo;
    }
}
