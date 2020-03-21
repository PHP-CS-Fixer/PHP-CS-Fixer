<?php

/**
 * Return types of protected functions.
 * Foo found by argument to public function
 */
class Case017
{
    private $foo;
    public function __construct()
    {
        $this->init();
    }

    protected function init(): Foo
    {
        $bar = new Bar();
        return $bar->createFoo();
    }

    public function other(Foo $input)
    {
        // Nothing
    }
}
