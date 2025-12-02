<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitExpectDeprecationFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitExpectDeprecationFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitExpectDeprecationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple deprecation message' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFoo()
        {
            $this->expectDeprecation(\'Deprecation message\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation Deprecation message
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'deprecation message with quotes' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFoo()
        {
            $this->expectDeprecation(\'Deprecation "message"\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation Deprecation "message"
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'deprecation message with regex characters' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFoo()
        {
            $this->expectDeprecation(\'Deprecated.*since 1.0\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation Deprecated.*since 1.0
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'multiple deprecation annotations' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFoo()
        {
            $this->expectDeprecation(\'First deprecation\');
            $this->expectDeprecation(\'Second deprecation\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation First deprecation
         * @expectedDeprecation Second deprecation
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'with other PHPDoc content' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Tests foo method
         */
        public function testFoo()
        {
            $this->expectDeprecation(\'Deprecation message\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Tests foo method
         * @expectedDeprecation Deprecation message
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'multiline annotation' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFoo()
        {
            $this->expectDeprecation(\'This is a very long deprecation message that spans multiple lines in the annotation\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation This is a very long deprecation message
         *     that spans multiple lines in the annotation
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'no annotation should not be changed' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Tests foo method
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'abstract function should not be changed' => [
            '<?php
    abstract class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation Deprecation message
         */
        abstract public function testFoo();
    }',
        ];

        yield 'with private visibility' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        private function testFoo()
        {
            $this->expectDeprecation(\'Deprecation message\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation Deprecation message
         */
        private function testFoo()
        {
            aaa();
        }
    }',
        ];

        yield 'empty annotation should not be changed' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedDeprecation
         */
        public function testFoo()
        {
            aaa();
        }
    }',
        ];
    }
}
