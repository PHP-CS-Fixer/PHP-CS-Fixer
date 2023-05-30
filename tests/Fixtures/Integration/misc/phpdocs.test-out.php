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
     * @param bool  $a   Test phpdoc_param_order
     * @param mixed $fo  this is mixed
     * @param int   $b   Test phpdoc_param_order
     * @param float $bar this is float
     * @param mixed $c
     * @param mixed $qux
     *
     * @throws Exception
     *
     * @custom
     */
    public function fooBar ($a, $fo, $b, $bar, array $baz, $c, $qux) {}
}
