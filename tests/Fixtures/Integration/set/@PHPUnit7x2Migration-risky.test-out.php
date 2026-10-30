<?php

class FooTest extends \PHPUnit\Framework\TestCase {
    /**
     */
    public function test_php_unit_no_expectation_annotation()
    {
        $this->expectException(\FooException::class);

        foo();
    }

    /**
     */
    public function test_php_unit_does_not_perform_assertions_annotation()
    {
        $this->expectNotToPerformAssertions();

        bar();
    }
}
