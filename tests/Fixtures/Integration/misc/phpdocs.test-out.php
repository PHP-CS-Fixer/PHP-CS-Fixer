<?php

class Foo {
    
    public $bar;

    /**
     * @var array
     */
    public $baz;

    /**
     * FooBar.
     *
     * @param int   $fo  this is int
     * @param float $bar this is float
     * @param mixed $qux
     *
     * @throws Exception
     *
     * @custom
     */
    public function fooBar ($fo, $bar, array $baz, $qux) {}
}
