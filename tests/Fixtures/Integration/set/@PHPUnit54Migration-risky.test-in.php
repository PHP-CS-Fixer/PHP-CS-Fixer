<?php

class FooTest extends \PHPUnit_Framework_TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertTrue(is_null($foo));
        $this->assertTrue(is_array($foo));
        $this->assertTrue(is_nan($foo));
        $this->assertTrue(is_readable($foo));
    }

    public function test_php_unit_expectation_52() {
        $this->setExpectedException("RuntimeException", "Msg", 123);
    }

    public function test_php_unit_expectation_56() {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
    }
}
