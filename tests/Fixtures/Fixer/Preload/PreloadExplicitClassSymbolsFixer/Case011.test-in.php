<?php

/**
 * Return types of public functions
 */
class Case011
{
    private $foo;
    public function __construct()
    {
        $this->init();
    }

    public function init(): Foo
    {
        $bar = new Bar();
        return $bar->createFoo();
    }
}
