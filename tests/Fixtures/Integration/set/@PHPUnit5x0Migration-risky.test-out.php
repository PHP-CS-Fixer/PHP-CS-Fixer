<?php

use PHPUnit_Framework_Assert;
use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestListener;
use PHPUnit_Aaa;
use PHPUnit_Aaa_Bbb;
use PHPUnit_Aaa_Bbb_Ccc;
use PHPUnit_Aaa_Bbb_Ccc_Ddd;
use PHPUnit_Aaa_Bbb_Ccc_Ddd_Eee;

class FooTest extends \PHPUnit\Framework\TestCase {
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
        $this->setExpectedException(\FooException::class, null, 123);

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
}
