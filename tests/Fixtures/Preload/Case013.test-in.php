<?php

/**
 * Return types of protected functions
 */
class Case013
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
}
