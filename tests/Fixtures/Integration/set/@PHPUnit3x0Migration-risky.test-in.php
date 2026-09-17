<?php

class FooTest extends \PHPUnit_Framework_TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertTrue(is_null($foo));
        $this->assertTrue(is_array($foo));
        $this->assertTrue(is_nan($foo));
        $this->assertTrue(is_readable($foo));
    }
}
