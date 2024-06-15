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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer
 */
final class PhpUnitMethodCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array{case?: 'camel_case'|'snake_case'} $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'skip non phpunit methods' => [
            '<?php class MyClass {
                    public function testMyApp() {}
                    public function test_my_app() {}
                }',
        ];

        yield 'skip non test methods' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function not_a_test() {}
                    public function notATestEither() {}
                }',
        ];

        foreach (self::pairs() as $key => [$camelCase, $snakeCase]) {
            yield $key.' to camel case' => [$camelCase, $snakeCase];

            yield $key.' to snake case' => [$snakeCase, $camelCase, ['case' => 'snake_case']];
        }

        yield 'mixed case to camel case' => [
            '<?php class MyTest extends TestCase { function testShouldNotFooWhenBar() {} }',
            '<?php class MyTest extends TestCase { function test_should_notFoo_When_Bar() {} }',
        ];

        yield 'mixed case to snake case' => [
            '<?php class MyTest extends TestCase { function test_should_not_foo_when_bar() {} }',
            '<?php class MyTest extends TestCase { function test_should_notFoo_When_Bar() {} }',
            ['case' => 'snake_case'],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield '@depends annotation with class name in Snake_Case' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                public function testMyApp () {}

                /**
                 * @depends Foo_Bar_Test::testMyApp
                 */
                #[SimpleTest]
                public function testMyAppToo() {}
            }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                public function test_my_app () {}

                /**
                 * @depends Foo_Bar_Test::test_my_app
                 */
                #[SimpleTest]
                public function test_my_app_too() {}
            }',
        ];

        yield '@depends annotation with class name in Snake_Case and attributes in between' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                public function testMyApp () {}

                /**
                 * @depends Foo_Bar_Test::testMyApp
                 */
                #[SimpleTest]
                #[Deprecated]
                public function testMyAppToo() {}
            }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                public function test_my_app () {}

                /**
                 * @depends Foo_Bar_Test::test_my_app
                 */
                #[SimpleTest]
                #[Deprecated]
                public function test_my_app_too() {}
            }',
        ];
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    private static function pairs(): iterable
    {
        yield 'default sample' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { public function testMyApp() {} }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { public function test_my_app() {} }',
        ];

        yield 'annotation' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function myApp() {} }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function my_app() {} }',
        ];

        yield '@depends annotation' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function testMyApp () {}

                    /**
                     * @depends testMyApp
                     */
                    public function testMyAppToo() {}
                }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function test_my_app () {}

                    /**
                     * @depends test_my_app
                     */
                    public function test_my_app_too() {}
                }',
        ];

        yield '@depends annotation with class name in PascalCase' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function testMyApp () {}

                    /**
                     * @depends FooBarTest::testMyApp
                     */
                    public function testMyAppToo() {}
                }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function test_my_app () {}

                    /**
                     * @depends FooBarTest::test_my_app
                     */
                    public function test_my_app_too() {}
                }',
        ];

        yield '@depends annotation with class name in Snake_Case' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function testMyApp () {}

                    /**
                     * @depends Foo_Bar_Test::testMyApp
                     */
                    public function testMyAppToo() {}
                }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function test_my_app () {}

                    /**
                     * @depends Foo_Bar_Test::test_my_app
                     */
                    public function test_my_app_too() {}
                }',
        ];

        yield '@depends and @test annotation' => [
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    /**
                     * @test
                     */
                    public function myApp () {}

                    /**
                     * @test
                     * @depends myApp
                     */
                    public function myAppToo() {}

                    /** not a test method */
                    public function my_app_not() {}

                    public function my_app_not_2() {}
                }',
            '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    /**
                     * @test
                     */
                    public function my_app () {}

                    /**
                     * @test
                     * @depends my_app
                     */
                    public function my_app_too() {}

                    /** not a test method */
                    public function my_app_not() {}

                    public function my_app_not_2() {}
                }',
        ];
    }
}
