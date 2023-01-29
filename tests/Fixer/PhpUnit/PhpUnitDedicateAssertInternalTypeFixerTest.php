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
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertInternalTypeFixer
 */
final class PhpUnitDedicateAssertInternalTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideTestFixInternalTypeCases
     */
    public function testFixInternalType(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideTestFixInternalTypeCases(): iterable
    {
        yield 'skip cases' => [
            '<?php
final class MyTest extends \PhpUnit\FrameWork\TestCase
{
    public function testMe()
    {
        $this->assertInternalType(gettype($expectedVar), $var);
        $this->assertNotInternalType(gettype($expectedVar), $var);

        $this->assertInternalType("foo", $var);
        $this->assertNotInternalType("bar", $var);

        $this->assertInternalType();
        $this->assertNotInternalType();

        $this->assertInternalType("array" . "foo", $var);
        $this->assertNotInternalType(\'bool\' . "bar", $var);
    }
}
',
        ];

        yield 'expected normal cases' => [
            '<?php
final class MyTest extends \PhpUnit\FrameWork\TestCase
{
    public function testMe()
    {
        $this->assertIsArray($var);
        $this->assertIsBool($var);
        $this->assertIsBool($var);
        $this->assertIsFloat($var);
        $this->assertIsFloat($var);
        $this->assertIsInt($var);
        $this->assertIsInt($var);
        $this->assertNull($var);
        $this->assertIsNumeric($var);
        $this->assertIsObject($var);
        $this->assertIsFloat($var);
        $this->assertIsResource($var);
        $this->assertIsString($var);
        $this->assertIsScalar($var);
        $this->assertIsCallable($var);
        $this->assertIsIterable($var);

        $this->assertIsNotArray($var);
        $this->assertIsNotBool($var);
        $this->assertIsNotBool($var);
        $this->assertIsNotFloat($var);
        $this->assertIsNotFloat($var);
        $this->assertIsNotInt($var);
        $this->assertIsNotInt($var);
        $this->assertNotNull($var);
        $this->assertIsNotNumeric($var);
        $this->assertIsNotObject($var);
        $this->assertIsNotFloat($var);
        $this->assertIsNotResource($var);
        $this->assertIsNotString($var);
        $this->assertIsNotScalar($var);
        $this->assertIsNotCallable($var);
        $this->assertIsNotIterable($var);
    }
}
',
            '<?php
final class MyTest extends \PhpUnit\FrameWork\TestCase
{
    public function testMe()
    {
        $this->assertInternalType(\'array\', $var);
        $this->assertInternalType("boolean", $var);
        $this->assertInternalType("bool", $var);
        $this->assertInternalType("double", $var);
        $this->assertInternalType("float", $var);
        $this->assertInternalType("integer", $var);
        $this->assertInternalType("int", $var);
        $this->assertInternalType("null", $var);
        $this->assertInternalType("numeric", $var);
        $this->assertInternalType("object", $var);
        $this->assertInternalType("real", $var);
        $this->assertInternalType("resource", $var);
        $this->assertInternalType("string", $var);
        $this->assertInternalType("scalar", $var);
        $this->assertInternalType("callable", $var);
        $this->assertInternalType("iterable", $var);

        $this->assertNotInternalType("array", $var);
        $this->assertNotInternalType("boolean", $var);
        $this->assertNotInternalType("bool", $var);
        $this->assertNotInternalType("double", $var);
        $this->assertNotInternalType("float", $var);
        $this->assertNotInternalType("integer", $var);
        $this->assertNotInternalType("int", $var);
        $this->assertNotInternalType("null", $var);
        $this->assertNotInternalType("numeric", $var);
        $this->assertNotInternalType("object", $var);
        $this->assertNotInternalType("real", $var);
        $this->assertNotInternalType("resource", $var);
        $this->assertNotInternalType("string", $var);
        $this->assertNotInternalType("scalar", $var);
        $this->assertNotInternalType("callable", $var);
        $this->assertNotInternalType("iterable", $var);
    }
}
',
        ];

        yield 'false positive cases' => [
            '<?php
final class MyTest extends \PhpUnit\FrameWork\TestCase
{
    public function testMe()
    {
        $this->assertInternalType = 42;
        $this->assertNotInternalType = 43;
    }
}
',
        ];

        yield 'anonymous class false positive case' => [
            '<?php
final class MyTest extends \PhpUnit\FrameWork\TestCase
{
    public function testMe()
    {
        $class = new class {
            private function assertInternalType()
            {}
            private function foo(){
                $this->assertInternalType("array", $var);
            }
        };
    }
}
',
        ];
    }
}
