<?php

use PHPUnit_Framework_Assert;
use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestListener;
use PHPUnit_Aaa;
use PHPUnit_Aaa_Bbb;
use PHPUnit_Aaa_Bbb_Ccc;
use PHPUnit_Aaa_Bbb_Ccc_Ddd;
use PHPUnit_Aaa_Bbb_Ccc_Ddd_Eee;

class FooTest extends \PHPUnit_Framework_TestCase {
    public function test_dedicate_assert($foo) {
        $this->assertTrue(is_null($foo));
        $this->assertTrue(is_array($foo));
        $this->assertTrue(is_nan($foo));
        $this->assertTrue(is_readable($foo));
    }

    /**
     * Foo.
     * @expectedException FooException
     * @expectedExceptionCode 123
     */
    function test_php_unit_no_expectation_annotation_32()
    {
        bbb();
    }

    /**
     * Foo.
     * @expectedException FooException
     * @expectedExceptionMessageRegExp /foo.*$/
     * @expectedExceptionCode 123
     */
    function test_php_unit_no_expectation_annotation_43()
    {
        ccc();
    }
}
