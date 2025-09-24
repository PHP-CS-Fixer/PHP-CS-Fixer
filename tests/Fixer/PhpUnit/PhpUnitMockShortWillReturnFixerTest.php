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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer>
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer
 *
 * @author Michał Adamski <michal.adamski@gmail.com>
 */
final class PhpUnitMockShortWillReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'do not fix' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->will;
        $someMock->method("someMethod")->will("Smith");
        $someMock->method("someMethod")->will($this->returnSelf);
        $someMock->method("someMethod")->will($this->doSomething(7));
    }
}',
        ];

        yield 'will return simple scenarios' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->willReturn(10);
        $someMock->method("someMethod")->willReturn(20);
        $someMock->method("someMethod")->willReturn(30);
        $someMock->method("someMethod")->willReturn(40);
        $someMock->method("someMethod")->willReturn(50);
        $someMock->method("someMethod")->willReturn(60);
        $someMock->method("someMethod")->willReturn(-10);
        $someMock->method("someMethod")->willReturn(10.10);
        $someMock->method("someMethod")->willReturn(-10.10);
        $someMock->method("someMethod")->willReturn("myValue");
        $someMock->method("someMethod")->willReturn($myValue);
        $testMock->method("test_method")->willReturn(DEFAULT_VALUE);
        $testMock->method("test_method")->willReturn(self::DEFAULT_VALUE);
        $someMock->method("someMethod")->willReturn([]);
        $someMock->method("someMethod")->willReturn([[]]);
        $someMock->method("someMethod")->willReturn(array());
        $someMock->method("someMethod")->willReturn(new stdClass());
        $someMock->method("someMethod")->willReturn(new \DateTime());
        $someMock->method("someMethod")->willReturnSelf();
        $someMock->method("someMethod")->willReturnArgument(2);
        $someMock->method("someMethod")->willReturnCallback("str_rot13");
        $someMock->method("someMethod")->willReturnMap(["a", "b", "c", "d"]);
        $someMock->method("someMethod")->willReturn(1);
        $someMock->method("someMethod")->willReturn(2);
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->will($this->returnValue(10));
        $someMock->method("someMethod")->will($THIS->returnValue(20));
        $someMock->method("someMethod")->will(self::returnValue(30));
        $someMock->method("someMethod")->will(Self::returnValue(40));
        $someMock->method("someMethod")->will(static::returnValue(50));
        $someMock->method("someMethod")->will(STATIC::returnValue(60));
        $someMock->method("someMethod")->will($this->returnValue(-10));
        $someMock->method("someMethod")->will($this->returnValue(10.10));
        $someMock->method("someMethod")->will($this->returnValue(-10.10));
        $someMock->method("someMethod")->will($this->returnValue("myValue"));
        $someMock->method("someMethod")->will($this->returnValue($myValue));
        $testMock->method("test_method")->will($this->returnValue(DEFAULT_VALUE));
        $testMock->method("test_method")->will($this->returnValue(self::DEFAULT_VALUE));
        $someMock->method("someMethod")->will($this->returnValue([]));
        $someMock->method("someMethod")->will($this->returnValue([[]]));
        $someMock->method("someMethod")->will($this->returnValue(array()));
        $someMock->method("someMethod")->will($this->returnValue(new stdClass()));
        $someMock->method("someMethod")->will($this->returnValue(new \DateTime()));
        $someMock->method("someMethod")->will($this->returnSelf());
        $someMock->method("someMethod")->will($this->returnArgument(2));
        $someMock->method("someMethod")->will($this->returnCallback("str_rot13"));
        $someMock->method("someMethod")->will($this->returnValueMap(["a", "b", "c", "d"]));
        $someMock->method("someMethod")->WILL($this->returnValue(1));
        $someMock->method("someMethod")->will($this->ReturnVALUE(2));
    }
}',
        ];

        yield 'will return with multi lines and messy indents' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock
        ->method("someMethod")
            ->willReturn(
                10
            );
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock
        ->method("someMethod")
            ->will(
                $this->returnValue(10)
            );
    }
}',
        ];

        yield 'will return with multi lines, messy indents and comments inside' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock
        ->method("someMethod")
            ->willReturn(
                // foo
                10
                // bar
            );
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock
        ->method("someMethod")
            ->will(
                // foo
                $this->returnValue(10)
                // bar
            );
    }
}',
        ];

        yield 'will return with block comments in weird places' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->/* a */willReturn/* b */(/* c */ 10 /* d */);
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->/* a */will/* b */(/* c */ $this->returnValue(10) /* d */);
    }
}',
        ];

        yield 'will return with comments persisted not touched even if put in unexpected places' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")// a
            ->/* b */willReturn/* c */(/* d */ /** e */
             // f
            '.'
            // g
            '.'
            /* h */
            10 /* i */);
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")// a
            ->/* b */will/* c */(/* d */ $this/** e */
            -> // f
            returnValue
            // g
            (
            /* h */
            10) /* i */);
    }
}',
        ];

        yield 'will return with multi lines, messy indents and comments in weird places' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock
        ->method(
        "someMethod"
        )
              ->

              /* a */
        willReturn
                /*
                b
                c
                d
                e
        */        (
                    // f g h i
                    /* j */ '.'
        '.'
            10
             /* k */
             /* l */);
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock
        ->method(
        "someMethod"
        )
              ->

              /* a */
        will
                /*
                b
                c
                d
                e
        */        (
                    // f g h i
                    /* j */ $this
        ->returnValue
            (10)
             /* k */
             /* l */);
    }
}',
        ];

        yield [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->willReturn( 10 , );
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->will($this->returnValue( 10 , ));
    }
}',
        ];

        yield 'with trailing commas' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->willReturn( 10 ,   );
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock->method("someMethod")->will($this->returnValue( 10 , ) , );
    }
}',
        ];
    }

    /**
     * @requires PHP 8.0
     */
    public function testFix80(): void
    {
        $this->doTest(
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock?->method("someMethod")?->willReturn(10);
    }
}',
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $someMock?->method("someMethod")?->will($this?->returnValue(10));
    }
}'
        );
    }

    /**
     * @requires PHP 8.1
     */
    public function testFix81(): void
    {
        $this->doTest(
            '<?php
class FooTest extends TestCase {
    public function testFoo() {
        $a = $someMock?->method("someMethod")->will($this?->returnValue(...));
    }
}'
        );
    }
}
