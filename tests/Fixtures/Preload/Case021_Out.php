<?php

/**
 * Make sure we dont use "parent" or "self"
 */
class Case021
{
    const FOO = 2;
    private $bar;
    public function __construct()
    {
        $this->bar = self::FOO;
        parent::__construct();
    }
}
