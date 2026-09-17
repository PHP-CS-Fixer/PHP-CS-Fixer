<?php

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestListener;
use PHPUnit\Aaa;
use PHPUnit\Aaa\Bbb;
use PHPUnit\Aaa\Bbb\Ccc;
use PHPUnit\Aaa\Bbb\Ccc\Ddd;
use PHPUnit\Aaa\Bbb\Ccc\Ddd\Eee;

class FooTest extends \PHPUnit\Framework\TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertNull($foo);
        $this->assertInternalType('array', $foo);
        $this->assertNan($foo);
        $this->assertIsReadable($foo);
    }

    /**
     * Foo.
     */
    function test_php_unit_no_expectation_annotation_32()
    {
        $this->expectException(\FooException::class);
        $this->expectExceptionCode(123);

        bbb();
    }

    /**
     * Foo.
     */
    function test_php_unit_no_expectation_annotation_43()
    {
        $this->expectException(\FooException::class);
        $this->expectExceptionMessageRegExp('/foo.*$/');
        $this->expectExceptionCode(123);

        ccc();
    }

    public function test_mock_54()
    {
        $mock = $this->createMock("Foo");
    }

    public function test_php_unit_expectation_52() {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessage("Msg");
        $this->expectExceptionCode(123);
    }

    public function test_php_unit_expectation_56() {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessageRegExp("/Msg.*/");
        $this->expectExceptionCode(123);
    }
}
