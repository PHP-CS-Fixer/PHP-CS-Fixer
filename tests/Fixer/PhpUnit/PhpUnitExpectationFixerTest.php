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
     * @param array<string, mixed> $config
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): array
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
            $this->expectException(\'RuntimeException\'/*B*/  /*B2*/);
            $this->expectExceptionCode(/*C*/123);
            zzz();
        }
        function testFnc2()
        {
            aaa();
            $this->expectException(\'RuntimeException\');
            $this->expectExceptionMessage(/*B*/ null /*B2*/ + 1);
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
            $this->setExpectedException(\'RuntimeException\',/*B*/ null /*B2*/,/*C*/123);
            zzz();
        }
        function testFnc2()
        {
            aaa();
            $this->setExpectedException(\'RuntimeException\',/*B*/ null /*B2*/ + 1,/*C*/123);
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
        $this->expectExceptionMessageMatches("/Msg.*/");
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
                ['target' => PhpUnitTargetVersion::VERSION_8_4],
            ],
            [
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->expectExceptionMessageMatches("/Msg.*/");
        foo();
    }
}',
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $this->expectExceptionMessageRegExp("/Msg.*/");
        foo();
    }
}',
                ['target' => PhpUnitTargetVersion::VERSION_8_4],
            ],
            [
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        // turns wrong into wrong: has a single argument only, but ...
        $this->expectExceptionMessageMatches("/Msg.*/");
        $this->expectExceptionMessageMatches("fail-case");
        foo();
    }
}',
                '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        // turns wrong into wrong: has a single argument only, but ...
        $this->expectExceptionMessageRegExp("/Msg.*/", "fail-case");
        foo();
    }
}',
                ['target' => PhpUnitTargetVersion::VERSION_8_4],
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
        $this->expectExceptionMessageMatches("/Msg.*/");
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
            [
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this->expectException("RuntimeException");
            $this->expectExceptionMessage("msg"/*B*/);
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
            $this->setExpectedException("RuntimeException", "msg"/*B*/, /*C*/123, );
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
            $this->expectException("RuntimeException");
            $this->expectExceptionMessage("msg"/*B*/);
            $this->expectExceptionCode(/*C*/123/*D*/);
            zzz();
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this->setExpectedException("RuntimeException", "msg"/*B*/, /*C*/123, /*D*/);
            zzz();
        }
    }',
            ],
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $expected = str_replace(['    ', "\n"], ["\t", "\r\n"], $expected);
        if (null !== $input) {
            $input = str_replace(['    ', "\n"], ["\t", "\r\n"], $input);
        }

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): array
    {
        $expectedTemplate =
'
        function testFnc%d()
        {
            aaa();
            $this->expectException(\'RuntimeException\');
            $this->expectExceptionMessage(\'msg\'/*B*/);
            $this->expectExceptionCode(/*C*/123);
            zzz();
        }
';
        $inputTemplate =
'
        function testFnc%d()
        {
            aaa();
            $this->setExpectedException(\'RuntimeException\', \'msg\'/*B*/, /*C*/123);
            zzz();
        }
'
        ;
        $input = $expected = '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
    ';

        for ($i = 0; $i < 8; ++$i) {
            $expected .= sprintf($expectedTemplate, $i);
            $input .= sprintf($inputTemplate, $i);
        }

        $expected .= "\n}";
        $input .= "\n}";

        return [[$expected, $input]];
    }

    /**
     * @requires PHP 8.0
     */
    public function testFix80(): void
    {
        $this->doTest(
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this?->expectException("RuntimeException");
            $this->expectExceptionMessage("message");
            $this->expectExceptionCode(123);
            zzz();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        function testFnc()
        {
            aaa();
            $this?->setExpectedException("RuntimeException", "message", 123);
            zzz();
        }
    }'
        );
    }
}
