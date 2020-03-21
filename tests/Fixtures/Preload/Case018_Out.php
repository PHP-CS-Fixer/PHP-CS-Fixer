<?php

class_exists(Foo::class);
class_exists(Bar::class);
/**
 * Return types of protected functions.
 * Foo not found by argument to protected function
 */
class Case018
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

    protected function other(Foo $input)
    {
        // Nothing
    }
}
