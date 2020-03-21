<?php

class_exists(Foo::class);
class_exists(Bar::class);
/**
 * Return types of protected functions.
 * Foo not found by argument to private function
 */
class Case019
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

    private function other(Foo $input)
    {
        // Nothing
    }
}
