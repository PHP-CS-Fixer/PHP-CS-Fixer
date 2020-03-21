<?php

class_exists(Bar::class);
/**
 * Return types of protected functions to return primary type
 */
class Case015
{
    private $foo;
    public function __construct()
    {
        $this->init();
    }

    protected function init(): string
    {
        $bar = new Bar();
        return $bar->createFoo();
    }
}
