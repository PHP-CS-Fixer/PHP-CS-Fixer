<?php

class Foo {
    /**
     * @access public
     */
    public $bar;

    /**
     * @type array $baz
     */


    public $baz;

/**
 *
 * FooBar
 *
 * @throws Exception
 * @param Mixed $fo This is mixed.
 *
 * @param float $bar This is float.
 * @param int $b Test phpdoc_param_order
 * @param bool $a Test phpdoc_param_order
 * @return void
 *
 *
 * @custom
 */
    public function fooBar ($a, $fo, $b, $bar, array $baz, $c, $qux) {}
}
