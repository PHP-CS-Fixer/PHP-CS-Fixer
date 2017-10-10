<?php

class FooTest extends \PHPUnit_Framework_TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertNull($foo);
        $this->assertInternalType('array', $foo);
        $this->assertNan($foo);
        $this->assertTrue(is_readable($foo));
    }

    public function test_php_unit_expectation_52() {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessage("Msg");
        $this->expectExceptionCode(123);
    }

    public function test_php_unit_expectation_56() {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
    }
}
