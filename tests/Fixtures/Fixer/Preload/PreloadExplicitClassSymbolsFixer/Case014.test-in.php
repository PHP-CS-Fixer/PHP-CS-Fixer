<?php

/**
 * Return types of protected functions to return self
 */
class Case014
{
    private $foo;
    public function __construct()
    {
        $this->init();
    }

    protected function init(): self
    {
        $bar = new Bar();
        return $bar->createFoo();
    }
}
