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
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitExpectationFixer
 */
final class PhpUnitExpectationFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     * @param array       $config
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
        function testFnc()
        {
            aaa();
            $this->expectException(\'RuntimeException\');
            $this->expectExceptionMessage(\'msg\'/*B*/);
            $this->expectExceptionCode(/*C*/123);
            zzz();
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this->setExpectedException(\'RuntimeException\', \'msg\'/*B*/, /*C*/123);
            zzz();
        }
    }',
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this->expectException(\'RuntimeException\');
            $this->expectExceptionMessage(null);
            $this->expectExceptionCode(/*C*/123);
            zzz();
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this->setExpectedException(\'RuntimeException\',null,/*C*/123);
            zzz();
        }
    }',
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            $this->expectException(
                \Exception::class
            );
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            $this->setExpectedException(
                \Exception::class
            );
        }
    }',
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            $this->expectException(
                \Exception::class
            );
            $this->expectExceptionMessage(
                "foo"
            );
            $this->expectExceptionCode(
                123
            );
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            $this->setExpectedException(
                \Exception::class,
                "foo",
                123
            );
        }
    }',
            ],
            [
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessage("Msg");
        $this->expectExceptionCode(123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}',
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", "Msg", 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}',
                ['target' => PhpUnitTargetVersion::VERSION_5_2],
            ],
            [
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessage("Msg");
        $this->expectExceptionCode(123);
        foo();
    }

    public function testBar()
    {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessageRegExp("/Msg.*/");
        $this->expectExceptionCode(123);
        bar();
    }
}',
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", "Msg", 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}',
                ['target' => PhpUnitTargetVersion::VERSION_5_6],
            ],
            [
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessage("Msg");
        $this->expectExceptionCode(123);
        foo();
    }

    public function testBar()
    {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessageRegExp("/Msg.*/");
        $this->expectExceptionCode(123);
        bar();
    }
}',
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->setExpectedException("RuntimeException", "Msg", 123);
        foo();
    }

    public function testBar()
    {
        $this->setExpectedExceptionRegExp("RuntimeException", "/Msg.*/", 123);
        bar();
    }
}',
                ['target' => PhpUnitTargetVersion::VERSION_NEWEST],
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $expected = str_replace(['    ', "\n"], ["\t", "\r\n"], $expected);
        if (null !== $input) {
            $input = str_replace(['    ', "\n"], ["\t", "\r\n"], $input);
        }

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this->expectException(\'RuntimeException\');
            $this->expectExceptionMessage(\'msg\'/*B*/);
            $this->expectExceptionCode(/*C*/123);
            zzz();
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this->setExpectedException(\'RuntimeException\', \'msg\'/*B*/, /*C*/123);
            zzz();
        }
    }',
            ],
        ];
    }
}
