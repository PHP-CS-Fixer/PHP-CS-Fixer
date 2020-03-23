<?php

class_exists(Foo::class);
class_exists(Bar::class);
/**
 * Return types of private functions
 */
class Case012
{
    private $foo;
    public function __construct()
    {
        $this->init();
    }

    private function init(): Foo
    {
        $bar = new Bar();
        return $bar->createFoo();
    }
}
