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
        $this->assertFalse(is_dir($foo));
        $this->assertFalse(is_writable($foo));
        $this->assertFalse(file_exists($foo));
        $this->assertFalse(is_readable($foo));
    }
}
