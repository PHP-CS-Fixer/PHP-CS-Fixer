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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\TestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer
 */
final class PhpUnitTestCaseStaticMethodCallsFixerTest extends AbstractFixerTestCase
{
    public function testFixerContainsAllPhpunitStaticMethodsInItsList(): void
    {
        $assertionRefClass = new \ReflectionClass(TestCase::class);
        $updatedStaticMethodsList = $assertionRefClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        $fixerRefClass = new \ReflectionClass(PhpUnitTestCaseStaticMethodCallsFixer::class);
        $defaultProperties = $fixerRefClass->getDefaultProperties();
        $staticMethods = $defaultProperties['staticMethods'];

        $missingMethods = [];
        foreach ($updatedStaticMethodsList as $method) {
            if ($method->isStatic() && !isset($staticMethods[$method->name])) {
                $missingMethods[] = $method->name;
            }
        }

        self::assertSame([], $missingMethods, sprintf('The following static methods from "%s" are missing from "%s::$staticMethods"', TestCase::class, PhpUnitTestCaseStaticMethodCallsFixer::class));
    }

    public function testWrongConfigTypeForMethodsKey(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/Unexpected "methods" key, expected any of ".*", got "integer#123"\.$/');

        $this->fixer->configure(['methods' => [123 => 1]]);
    }

    public function testWrongConfigTypeForMethodsValue(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/Unexpected value for method "assertSame", expected any of ".*", got "integer#123"\.$/');

        $this->fixer->configure(['methods' => ['assertSame' => 123]]);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        static::assertSame(1, 2);
                        static::markTestIncomplete('foo');
                        static::fail('foo');
                    }
                }
                EOF
            ,
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        $this->assertSame(1, 2);
                        $this->markTestIncomplete('foo');
                        $this->fail('foo');
                    }
                }
                EOF
            ,
        ];

        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testMocks()
                    {
                        $mock = $this->createMock(MyInterface::class);
                        $mock
                            ->expects(static::once())
                            ->method('run')
                            ->with(
                                static::identicalTo(1),
                                static::stringContains('foo')
                            )
                            ->will(static::onConsecutiveCalls(
                                static::returnSelf(),
                                static::throwException(new \Exception())
                            ))
                        ;
                    }
                }
                EOF
            ,
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testMocks()
                    {
                        $mock = $this->createMock(MyInterface::class);
                        $mock
                            ->expects($this->once())
                            ->method('run')
                            ->with(
                                $this->identicalTo(1),
                                $this->stringContains('foo')
                            )
                            ->will($this->onConsecutiveCalls(
                                $this->returnSelf(),
                                $this->throwException(new \Exception())
                            ))
                        ;
                    }
                }
                EOF
            ,
        ];

        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testWeirdIndentation()
                    {
                        static
                        // @TODO
                            ::
                        assertSame
                        (1, 2);
                        // $this->markTestIncomplete('foo');
                        /*
                        $this->fail('foo');
                        */
                    }
                }
                EOF
            ,
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testWeirdIndentation()
                    {
                        $this
                        // @TODO
                            ->
                        assertSame
                        (1, 2);
                        // $this->markTestIncomplete('foo');
                        /*
                        $this->fail('foo');
                        */
                    }
                }
                EOF
            ,
        ];

        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        $this->assertSame(1, 2);
                        $this->markTestIncomplete('foo');
                        $this->fail('foo');

                        $lambda = function () {
                            $this->assertSame(1, 23);
                            self::assertSame(1, 23);
                            static::assertSame(1, 23);
                        };
                    }
                }
                EOF
            ,
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        $this->assertSame(1, 2);
                        self::markTestIncomplete('foo');
                        static::fail('foo');

                        $lambda = function () {
                            $this->assertSame(1, 23);
                            self::assertSame(1, 23);
                            static::assertSame(1, 23);
                        };
                    }
                }
                EOF
            ,
            ['call_type' => PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_THIS],
        ];

        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        self::assertSame(1, 2);
                        self::markTestIncomplete('foo');
                        self::fail('foo');
                    }
                }
                EOF
            ,
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        $this->assertSame(1, 2);
                        self::markTestIncomplete('foo');
                        static::fail('foo');
                    }
                }
                EOF
            ,
            ['call_type' => PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_SELF],
        ];

        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        $this->assertSame(1, 2);
                        $this->assertSame(1, 2);

                        static::setUpBeforeClass();
                        static::setUpBeforeClass();

                        $otherTest->setUpBeforeClass();
                        OtherTest::setUpBeforeClass();
                    }
                }
                EOF
            ,
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function testBaseCase()
                    {
                        static::assertSame(1, 2);
                        $this->assertSame(1, 2);

                        static::setUpBeforeClass();
                        $this->setUpBeforeClass();

                        $otherTest->setUpBeforeClass();
                        OtherTest::setUpBeforeClass();
                    }
                }
                EOF
            ,
            [
                'call_type' => PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_THIS,
                'methods' => ['setUpBeforeClass' => PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_STATIC],
            ],
        ];

        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public static function foo()
                    {
                        $this->assertSame(1, 2);
                        self::assertSame(1, 2);
                        static::assertSame(1, 2);

                        $lambda = function () {
                            $this->assertSame(1, 2);
                            self::assertSame(1, 2);
                            static::assertSame(1, 2);
                        };
                    }

                    public function bar()
                    {
                        $lambda = static function () {
                            $this->assertSame(1, 2);
                            self::assertSame(1, 2);
                            static::assertSame(1, 2);
                        };

                        $myProphecy->setCount(0)->will(function () {
                            $this->getCount()->willReturn(0);
                        });
                    }

                    static public function baz()
                    {
                        $this->assertSame(1, 2);
                        self::assertSame(1, 2);
                        static::assertSame(1, 2);

                        $lambda = function () {
                            $this->assertSame(1, 2);
                            self::assertSame(1, 2);
                            static::assertSame(1, 2);
                        };
                    }

                    static final protected function xyz()
                    {
                        static::assertSame(1, 2);
                    }
                }
                EOF
            ,
            null,
            [
                'call_type' => PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_THIS,
            ],
        ];

        yield [
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function foo()
                    {
                        $this->assertSame(1, 2);
                        $this->assertSame(1, 2);
                        $this->assertSame(1, 2);
                    }

                    public function bar()
                    {
                        $lambdaOne = static function () {
                            $this->assertSame(1, 21);
                            self::assertSame(1, 21);
                            static::assertSame(1, 21);
                        };

                        $lambdaTwo = function () {
                            $this->assertSame(1, 21);
                            self::assertSame(1, 21);
                            static::assertSame(1, 21);
                        };
                    }

                    public function baz2()
                    {
                        $this->assertSame(1, 22);
                        $this->assertSame(1, 22);
                        $this->assertSame(1, 22);
                        $this->assertSame(1, 23);
                    }

                }
                EOF
            ,
            <<<'EOF'
                <?php
                class MyTest extends \PHPUnit_Framework_TestCase
                {
                    public function foo()
                    {
                        $this->assertSame(1, 2);
                        self::assertSame(1, 2);
                        static::assertSame(1, 2);
                    }

                    public function bar()
                    {
                        $lambdaOne = static function () {
                            $this->assertSame(1, 21);
                            self::assertSame(1, 21);
                            static::assertSame(1, 21);
                        };

                        $lambdaTwo = function () {
                            $this->assertSame(1, 21);
                            self::assertSame(1, 21);
                            static::assertSame(1, 21);
                        };
                    }

                    public function baz2()
                    {
                        $this->assertSame(1, 22);
                        self::assertSame(1, 22);
                        static::assertSame(1, 22);
                        STATIC::assertSame(1, 23);
                    }

                }
                EOF
            ,
            [
                'call_type' => PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_THIS,
            ],
        ];

        yield 'do not change class property and method signature' => [
            <<<'EOF'
                <?php
                class FooTest extends TestCase
                {
                    public function foo()
                    {
                        $this->assertSame = 42;
                    }

                    public function assertSame($foo, $bar){}
                }
                EOF
            ,
        ];

        yield 'do not change when only case is different' => [
            <<<'EOF'
                <?php
                class FooTest extends TestCase
                {
                    public function foo()
                    {
                        STATIC::assertSame(1, 1);
                    }
                }
                EOF
            ,
        ];

        yield 'do not crash on abstract static function' => [
            <<<'EOF'
                <?php
                abstract class FooTest extends TestCase
                {
                    abstract public static function dataProvider();
                }
                EOF
            ,
            null,
            [
                'call_type' => PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_THIS,
            ],
        ];

        yield 'handle $this with double colon following' => [
            '<?php
                class FooTest extends TestCase
                {
                    public function testFoo()
                    {
                        static::assertTrue(true);
                    }
                }',
            '<?php
                class FooTest extends TestCase
                {
                    public function testFoo()
                    {
                        $this::assertTrue(true);
                    }
                }',
        ];
    }

    public function testAnonymousClassFixing(): void
    {
        $this->doTest(
            '<?php
class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testBaseCase()
    {
        static::assertSame(1, 2);

        $foo = new class() {
            public function assertSame($a, $b)
            {
                $this->assertSame(1, 2);
            }
        };
    }
}',
            '<?php
class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testBaseCase()
    {
        $this->assertSame(1, 2);

        $foo = new class() {
            public function assertSame($a, $b)
            {
                $this->assertSame(1, 2);
            }
        };
    }
}'
        );
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
                class FooTest extends TestCase
                {
                    public function testFoo()
                    {
                        $a = $this::assertTrue(...);
                    }
                }
            ',
        ];
    }
}
