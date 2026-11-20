<?php

class FooTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException FooException
     */
    public function test_php_unit_no_expectation_annotation()
    {
        foo();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_php_unit_does_not_perform_assertions_annotation()
    {
        bar();
    }
}
