<?php

class FooTest extends \PHPUnit_Framework_TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertNull($foo);
        $this->assertInternalType('array', $foo);
        $this->assertNan($foo);
        $this->assertTrue(is_readable($foo));
    }

    /**
     * Foo.
     */
    function test_php_unit_no_expectation_annotation_32()
    {
        $this->expectException(\FooException::class);
        $this->expectExceptionMessage('');
        $this->expectExceptionCode(123);
        bbb();
    }

    /**
     * Foo.
     */
    function test_php_unit_no_expectation_annotation_43()
    {
        $this->setExpectedExceptionRegExp(\FooException::class, '/foo.*$/', 123);
        ccc();
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
