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

use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitMockFixer
 */
final class PhpUnitMockFixerTest extends AbstractFixerTestCase
{
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
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
            $this->createMock("Foo");
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
            $this->getMockWithoutInvokingTheOriginalConstructor("Foo");
        }
    }',
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
            $this->createMock("Foo");
            $this->createMock($foo(1, 2));
            $this->getMock("Foo", ["aaa"]);
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
            $this->getMock("Foo");
            $this->getMock($foo(1, 2));
            $this->getMock("Foo", ["aaa"]);
        }
    }',
                ['target' => PhpUnitTargetVersion::VERSION_5_4],
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
            $this->createMock("Foo");
            $this->createMock($foo(1, 2));
            $this->createPartialMock("Foo", ["aaa"]);
            $this->getMock("Foo", ["aaa"], ["argument"]);
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testFoo()
        {
            $this->getMock("Foo");
            $this->getMock($foo(1, 2));
            $this->getMock("Foo", ["aaa"]);
            $this->getMock("Foo", ["aaa"], ["argument"]);
        }
    }',
            ],
        ];
    }

    /**
     * @requires PHP 7.3
     */
    public function testFix73()
    {
        $this->doTest(
            '<?php
    class FooTest extends TestCase
    {
        public function testFoo()
        {
            $this->createMock("Foo",);
            $this->createMock("Bar"  ,);
            $this->createMock("Baz"  ,  );
            $this->createMock($foo(1, 2), );
            $this->createMock($foo(3, 4, ));
            $this->createMock($foo(5, 6, ), );
            $this->createPartialMock("Foo", ["aaa"], );
            $this->createPartialMock("Foo", ["bbb", ], );
            $this->getMock("Foo", ["aaa"], ["argument"], );
            $this->getMock("Foo", ["bbb", ], ["argument", ], );
        }
    }',
            '<?php
    class FooTest extends TestCase
    {
        public function testFoo()
        {
            $this->getMock("Foo",);
            $this->getMock("Bar"  ,);
            $this->getMock("Baz"  ,  );
            $this->getMock($foo(1, 2), );
            $this->getMock($foo(3, 4, ));
            $this->getMock($foo(5, 6, ), );
            $this->getMock("Foo", ["aaa"], );
            $this->getMock("Foo", ["bbb", ], );
            $this->getMock("Foo", ["aaa"], ["argument"], );
            $this->getMock("Foo", ["bbb", ], ["argument", ], );
        }
    }'
        );
    }
}
