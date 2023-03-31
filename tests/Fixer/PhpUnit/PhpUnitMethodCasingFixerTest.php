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

use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
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
     * @dataProvider provideFixCases
     */
    public function testFixToCamelCase(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFixToSnakeCase(string $camelExpected, ?string $camelInput = null): void
    {
        if (null === $camelInput) {
            $expected = $camelExpected;
            $input = $camelInput;
        } else {
            $expected = $camelInput;
            $input = $camelExpected;
        }

        $this->fixer->configure(['case' => PhpUnitMethodCasingFixer::SNAKE_CASE]);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            'skip non phpunit methods' => [
                '<?php class MyClass {
                    public function testMyApp() {}
                    public function test_my_app() {}
                }',
            ],
            'skip non test methods' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function not_a_test() {}
                    public function notATestEither() {}
                }',
            ],
            'default sample' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { public function testMyApp() {} }',
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { public function test_my_app() {} }',
            ],
            'annotation' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function myApp() {} }',
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function my_app() {} }',
            ],
            '@depends annotation' => [
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
            ],
            '@depends annotation with class name in PascalCase' => [
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
            ],
            '@depends annotation with class name in Snake_Case' => [
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
            ],
            '@depends and @test annotation' => [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFix80ToCamelCaseCases
     *
     * @requires PHP 8.0
     */
    public function testFix80ToCamelCase(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string[]>
     */
    public static function provideFix80ToCamelCaseCases(): iterable
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
}
