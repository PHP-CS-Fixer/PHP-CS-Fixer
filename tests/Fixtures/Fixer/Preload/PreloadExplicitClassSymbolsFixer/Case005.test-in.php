<?php

use App\Foo;

/**
 * Constructor using more complex class references
 */
class Case005
{
    private $baz;
    private $bazClass;
    private $bar;
    private $barClass;
    public function __construct()
    {
        $this->bar = new Foo\Bar();
        $this->barClass = Foo\BarClass::class;
        $this->baz = new \Biz\Baz();
        $this->bazClass = \Biz\BazClass::class;
    }
}
