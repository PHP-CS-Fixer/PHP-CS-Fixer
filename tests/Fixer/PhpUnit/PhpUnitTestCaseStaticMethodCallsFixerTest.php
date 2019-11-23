<?php

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
    public function testFixerContainsAllPhpunitStaticMethodsInItsList()
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

        static::assertSame([], $missingMethods, sprintf('The following static methods from "%s" are missing from "%s::$staticMethods"', TestCase::class, PhpUnitTestCaseStaticMethodCallsFixer::class));
    }

    public function testWrongConfigTypeForMethodsKey()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/Unexpected "methods" key, expected any of ".*", got "integer#123"\.$/');

        $this->fixer->configure(['methods' => [123 => 1]]);
    }

    public function testWrongConfigTypeForMethodsValue()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/Unexpected value for method "assertSame", expected any of ".*", got "integer#123"\.$/');

        $this->fixer->configure(['methods' => ['assertSame' => 123]]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            'do not change class property and method signature' => [
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
            ],
            'do not change when only case is different' => [
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
            ],
        ];
    }

    /**
     * @requires PHP 7.0
     */
    public function testAnonymousClassFixing()
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
}
