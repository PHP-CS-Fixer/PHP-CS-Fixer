<?php

use App\Foo;

class_exists(Foo\Bar::class);
class_exists(Foo\BarClass::class);
class_exists(\Biz\Baz::class);
class_exists(\Biz\BazClass::class);
/**
 * Constructor using more complex class references
 */
class Case010
{
    private $baz;
    private $bazClass;
    private $bar;
    private $barClass;
    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->bar = new Foo\Bar();
        $this->barClass = Foo\BarClass::class;
        $this->baz = new \Biz\Baz();
        $this->bazClass = \Biz\BazClass::class;
    }
}
