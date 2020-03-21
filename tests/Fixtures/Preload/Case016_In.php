<?php

/**
 * Return types of protected functions to return nullable
 */
class Case016
{
    private $foo;
    public function __construct()
    {
        $this->init();
    }

    protected function init(): ?Foo
    {
        $bar = new Bar();
        return $bar->createFoo();
    }
}
