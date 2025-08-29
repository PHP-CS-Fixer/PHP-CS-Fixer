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
        $this->assertDirectoryDoesNotExist($foo);
        $this->assertIsNotWritable($foo);
        $this->assertFileDoesNotExist($foo);
        $this->assertIsNotReadable($foo);
    }
}
