<?php

class FooTest extends \PHPUnit_Framework_TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertNull($foo);
        $this->assertTrue(is_array($foo));
        $this->assertTrue(is_nan($foo));
        $this->assertTrue(is_readable($foo));
    }

    /**
     * Foo.
     */
    function test_php_unit_no_expectation_annotation_32()
    {
        $this->setExpectedException(\FooException::class, null, 123);
        bbb();
    }
}
