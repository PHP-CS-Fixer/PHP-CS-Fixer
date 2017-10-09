<?php

class FooTest extends \PHPUnit\Framework\TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertNull($foo);
        $this->assertTrue(is_array($foo));
        $this->assertTrue(is_nan($foo));
        $this->assertTrue(is_readable($foo));
    }
}
