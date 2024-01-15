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
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer
 */
final class PhpUnitDedicateAssertFixerTest extends AbstractFixerTestCase
{
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
            self::generateTest(<<<'EOD'

                                    $this->assertNan($a);
                                    $this->assertNan($a);
                                    $this->assertTrue(test\is_nan($a));
                                    $this->assertTrue(test\a\is_nan($a));
                EOD."\n                "),
            self::generateTest(<<<'EOD'

                                    $this->assertTrue(is_nan($a));
                                    $this->assertTrue(\is_nan($a));
                                    $this->assertTrue(test\is_nan($a));
                                    $this->assertTrue(test\a\is_nan($a));
                EOD."\n                "),
        ];

        yield [
            self::generateTest(<<<'EOD'

                                    $this->assertFileExists($a);
                                    $this->assertFileNotExists($a);
                                    $this->assertFileExists($a);
                                    $this->assertFileNotExists($a);
                EOD."\n                "),
            self::generateTest(<<<'EOD'

                                    $this->assertTrue(file_exists($a));
                                    $this->assertFalse(file_exists($a));
                                    $this->assertTrue(\file_exists($a));
                                    $this->assertFalse(\file_exists($a));
                EOD."\n                "),
        ];

        yield [
            self::generateTest(<<<'EOD'

                                    $this->assertNull($a);
                                    $this->assertNotNull($a);
                                    $this->assertNull($a);
                                    $this->assertNotNull($a, "my message");
                EOD."\n                "),
            self::generateTest(<<<'EOD'

                                    $this->assertTrue(is_null($a));
                                    $this->assertFalse(is_null($a));
                                    $this->assertTrue(\is_null($a));
                                    $this->assertFalse(\is_null($a), "my message");
                EOD."\n                "),
        ];

        yield [
            self::generateTest(<<<'EOD'

                                    $this->assertEmpty($a);
                                    $this->assertNotEmpty($a);
                EOD."\n                "),
            self::generateTest(<<<'EOD'

                                    $this->assertTrue(empty($a));
                                    $this->ASSERTFALSE(empty($a));
                EOD."\n                "),
        ];

        yield [
            self::generateTest(<<<'EOD'

                                    $this->assertInfinite($a);
                                    $this->assertFinite($a, "my message");
                                    $this->assertInfinite($a);
                                    $this->assertFinite($a, b"my message");
                EOD."\n                "),
            self::generateTest(<<<'EOD'

                                    $this->assertTrue(is_infinite($a));
                                    $this->assertFalse(is_infinite($a), "my message");
                                    $this->assertTrue(\is_infinite($a));
                                    $this->assertFalse(\is_infinite($a), b"my message");
                EOD."\n                "),
        ];

        yield [
            self::generateTest(<<<'EOD'

                                    $this->assertArrayHasKey("test", $a);
                                    $this->assertArrayNotHasKey($b, $a, $c);
                EOD."\n                "),
            self::generateTest(<<<'EOD'

                                    $this->assertTrue(\array_key_exists("test", $a));
                                    $this->ASSERTFALSE(array_key_exists($b, $a), $c);
                EOD."\n                "),
        ];

        yield [
            self::generateTest(<<<'EOD'

                $this->assertTrue(is_dir($a));
                $this->assertTrue(is_writable($a));
                $this->assertTrue(is_readable($a));

                EOD),
            null,
            ['target' => PhpUnitTargetVersion::VERSION_5_0],
        ];

        yield [
            self::generateTest(<<<'EOD'

                $this->assertTrue(is_dir($a));
                $this->assertTrue(is_writable($a));
                $this->assertTrue(is_readable($a));

                EOD),
            null,
            ['target' => PhpUnitTargetVersion::VERSION_3_0],
        ];

        yield [
            self::generateTest(<<<'EOD'

                $this->assertDirectoryNotExists($a);
                $this->assertNotIsWritable($a);
                $this->assertNotIsReadable($a);

                EOD),
            self::generateTest(<<<'EOD'

                $this->assertFalse(is_dir($a));
                $this->assertFalse(is_writable($a));
                $this->assertFalse(is_readable($a));

                EOD),
            ['target' => PhpUnitTargetVersion::VERSION_5_6],
        ];

        yield [
            self::generateTest(<<<'EOD'

                $this->assertDirectoryExists($a);
                $this->assertIsWritable($a);
                $this->assertIsReadable($a);

                EOD),
            self::generateTest(<<<'EOD'

                $this->assertTrue(is_dir($a));
                $this->assertTrue(is_writable($a));
                $this->assertTrue(is_readable($a));

                EOD),
            ['target' => PhpUnitTargetVersion::VERSION_NEWEST],
        ];

        foreach (['array', 'bool', 'callable', 'double', 'float', 'int', 'integer', 'long', 'numeric', 'object', 'real', 'scalar', 'string'] as $type) {
            yield [
                self::generateTest(sprintf('$this->assertInternalType(\'%s\', $a);', $type)),
                self::generateTest(sprintf('$this->assertTrue(is_%s($a));', $type)),
            ];

            yield [
                self::generateTest(sprintf('$this->assertNotInternalType(\'%s\', $a);', $type)),
                self::generateTest(sprintf('$this->assertFalse(is_%s($a));', $type)),
            ];
        }

        yield [
            self::generateTest('$this->assertInternalType(\'float\', $a, "my message");'),
            self::generateTest('$this->assertTrue(is_float( $a), "my message");'),
        ];

        yield [
            self::generateTest('$this->assertInternalType(\'float\', $a);'),
            self::generateTest('$this->assertTrue(\IS_FLOAT($a));'),
        ];

        yield [
            self::generateTest(<<<'EOD'
                $this->assertInternalType(#
                'float'#
                , #
                $a#
                #
                )#
                ;
                EOD),
            self::generateTest(<<<'EOD'
                $this->assertTrue(#
                \IS_FLOAT#
                (#
                $a#
                )#
                )#
                ;
                EOD),
        ];

        yield [
            self::generateTest('static::assertInternalType(\'float\', $a);'),
            self::generateTest('static::assertTrue(is_float( $a));'),
        ];

        yield [
            self::generateTest('self::assertInternalType(\'float\', $a);'),
            self::generateTest('self::assertTrue(is_float( $a));'),
        ];

        yield [
            self::generateTest('static::assertNull($a);'),
            self::generateTest('static::assertTrue(is_null($a));'),
        ];

        yield [
            self::generateTest('self::assertNull($a);'),
            self::generateTest('self::assertTrue(is_null($a));'),
        ];

        yield [
            self::generateTest('SELF::assertNull($a);'),
            self::generateTest('SELF::assertTrue(is_null($a));'),
        ];

        yield [
            self::generateTest('self::assertStringContainsString($needle, $haystack);'),
            self::generateTest('self::assertTrue(str_contains($haystack, $needle));'),
            ['target' => PhpUnitTargetVersion::VERSION_NEWEST],
        ];

        yield [
            self::generateTest('self::assertStringNotContainsString($needle, $a[$haystack.""](123)[foo()]);'),
            self::generateTest('self::assertFalse(str_contains($a[$haystack.""](123)[foo()], $needle));'),
            ['target' => PhpUnitTargetVersion::VERSION_NEWEST],
        ];

        yield [
            self::generateTest('self::assertStringStartsWith($needle, $haystack);'),
            self::generateTest('self::assertTrue(str_starts_with($haystack, $needle));'),
        ];

        yield [
            self::generateTest('self::assertStringStartsNotWith($needle, $haystack);'),
            self::generateTest('self::assertFalse(str_starts_with($haystack, $needle));'),
        ];

        yield [
            self::generateTest(<<<'EOD'
                self::assertStringStartsNotWith(  #3
                            $needle#4
                            , #1
                            $haystack#2
                            );
                EOD),
            self::generateTest(<<<'EOD'
                self::assertFalse(str_starts_with(  #1
                            $haystack#2
                            ,#3
                            $needle#4
                            ));
                EOD),
        ];

        yield [
            self::generateTest('self::assertStringEndsWith($needle, $haystack);'),
            self::generateTest('self::assertTrue(str_ends_with($haystack, $needle));'),
        ];

        yield [
            self::generateTest('self::assertStringEndsNotWith($needle, $haystack);'),
            self::generateTest('self::assertFalse(str_ends_with($haystack, $needle));'),
        ];

        yield '$a instanceof class' => [
            self::generateTest(<<<'EOD'

                                $this->assertInstanceOf(SomeClass::class, $x);
                                $this->assertInstanceOf(SomeClass::class, $y, $message);
                EOD."\n            "),
            self::generateTest(<<<'EOD'

                                $this->assertTrue($x instanceof SomeClass);
                                $this->assertTrue($y instanceof SomeClass, $message);
                EOD."\n            "),
        ];

        yield '$a instanceof class\a\b' => [
            self::generateTest(<<<'EOD'

                                $this->assertInstanceOf(\PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest\SimpleFixer::class, $ii);
                EOD."\n            "),
            self::generateTest(<<<'EOD'

                                $this->assertTrue($ii instanceof \PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest\SimpleFixer);
                EOD."\n            "),
        ];

        yield '$a instanceof $b' => [
            self::generateTest(<<<'EOD'

                                $this->assertInstanceOf($tty, $abc/* 1 *//* 2 */);
                                $this->assertInstanceOf($oo, $def, $message);
                EOD."\n            "),
            self::generateTest(<<<'EOD'

                                $this->assertTrue($abc instanceof /* 1 */$tty /* 2 */);
                                $this->assertTrue($def instanceof $oo, $message);
                EOD."\n            "),
        ];

        yield 'do not fix instance of' => [
            self::generateTest(<<<'EOD'

                                $this->assertTrue($gg instanceof $ijl . "X", $something);
                                $this->assertTrue($ff instanceof $klh . $b(1,2,$message), $noMsg);
                EOD."\n            "),
        ];

        yield '!$a instanceof class' => [
            self::generateTest(<<<'EOD'

                                $this->assertNotInstanceOf(SomeClass::class, $x);
                                $this->assertNotInstanceOf(SomeClass::class, $y, $message);
                EOD."\n            "),
            self::generateTest(<<<'EOD'

                                $this->assertTrue(!$x instanceof SomeClass);
                                $this->assertTrue(!$y instanceof SomeClass, $message);
                EOD."\n            "),
        ];
    }

    /**
     * @dataProvider provideNotFixCases
     */
    public function testNotFix(string $expected): void
    {
        $this->fixer->configure(['target' => PhpUnitTargetVersion::VERSION_NEWEST]);
        $this->doTest($expected);
    }

    public static function provideNotFixCases(): iterable
    {
        yield 'not a method call' => [
            self::generateTest('echo $this->assertTrue;'),
        ];

        yield 'wrong argument count 1' => [
            self::generateTest('static::assertTrue(is_null($a, $b));'),
        ];

        yield 'wrong argument count 2' => [
            self::generateTest('static::assertTrue(is_int($a, $b));'),
        ];

        yield [
            self::generateTest(<<<'EOD'

                                $this->assertTrue(is_null);
                                $this->assertTrue(is_int($a) && $b);
                                $this->assertFalse(is_nan($a));
                                $this->assertTrue(is_int($a) || \is_bool($b));
                                $this->assertTrue($a&&is_int($a));
                                static::assertTrue(is_null);
                                self::assertTrue(is_null);
                EOD."\n            "),
        ];

        yield 'not in class' => [
            '<?php self::assertTrue(is_null($a));',
        ];

        // Do not replace is_resource() by assertIsResource().
        // is_resource() also checks if the resource is open or closed,
        // while assertIsResource() does not.
        yield 'Do not replace is_resource' => [
            self::generateTest('self::assertTrue(is_resource($resource));'),
        ];
    }

    public function testInvalidConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[php_unit_dedicate_assert\] Invalid configuration: The option "target" .*\.$/');

        $this->fixer->configure(['target' => '_unknown_']);
    }

    /**
     * @dataProvider provideTestAssertCountCases
     */
    public function testAssertCount(string $expected, ?string $input = null): void
    {
        if (null === $input) {
            $expected = sprintf($expected, 'count');
        } else {
            $input = sprintf($input, 'count');
        }

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideTestAssertCountCases
     */
    public function testAssertCountFromSizeOf(string $expected, ?string $input = null): void
    {
        if (null === $input) {
            $expected = sprintf($expected, 'sizeof');
        } else {
            $input = sprintf($input, 'sizeof');
        }

        $this->doTest($expected, $input);
    }

    public static function provideTestAssertCountCases(): iterable
    {
        // positive fixing
        yield 'assert same' => [
            self::generateTest('$this->assertCount(1, $a);'),
            self::generateTest('$this->assertSame(1, %s($a));'),
        ];

        yield 'assert equals' => [
            self::generateTest('$this->assertCount(2, $b);'),
            self::generateTest('$this->assertEquals(2, %s($b));'),
        ];

        // negative fixing
        yield 'assert not same' => [
            self::generateTest('$this->assertNotCount(11, $c);'),
            self::generateTest('$this->assertNotSame(11, %s($c));'),
        ];

        yield 'assert not equals' => [
            self::generateTest('$this->assertNotCount(122, $d);'),
            self::generateTest('$this->assertNotEquals(122, %s($d));'),
        ];

        // other cases
        yield 'assert same with namespace' => [
            self::generateTest('$this->assertCount(1, $a);'),
            self::generateTest('$this->assertSame(1, \%s($a));'),
        ];

        yield 'no spacing' => [
            self::generateTest('$this->assertCount(1,$a);'),
            self::generateTest('$this->assertSame(1,%s($a));'),
        ];

        yield 'lot of spacing' => [
            self::generateTest(<<<'EOD'
                $this->assertCount(
                                1
                                ,
                EOD."\n                ".''."\n                ".<<<'EOD'

                                $a
                EOD."\n                ".<<<'EOD'

                                )
                                ;
                EOD),
            self::generateTest(<<<'EOD'
                $this->assertSame(
                                1
                                ,
                                %s
                                (
                                $a
                                )
                                )
                                ;
                EOD),
        ];

        yield 'lot of fix cases' => [
            self::generateTest(<<<'EOD'

                                    $this->assertCount(1, $a);
                                    $this->assertCount(2, $a);
                                    $this->assertCount(3, $a);
                                    $this->assertNotCount(4, $a);
                                    $this->assertCount(5, $a, "abc");
                                    $this->assertCount(6, $a, "def");
                EOD."\n                "),
            self::generateTest(<<<'EOD'

                                    $this->assertSame(1, %1$s($a));
                                    $this->assertSame(2, %1$s($a));
                                    $this->assertEquals(3, %1$s($a));
                                    $this->assertNotSame(4, %1$s($a));
                                    $this->assertEquals(5, %1$s($a), "abc");
                                    $this->assertSame(6, \%1$s($a), "def");
                EOD."\n                "),
        ];

        yield 'comment handling' => [
            self::generateTest(<<<'EOD'
                $this->assertCount(# 0
                1# 1
                ,# 2
                # 3
                # 4
                $a# 5
                # 6
                )# 7
                ;# 8
                EOD),
            self::generateTest(<<<'EOD'
                $this->assertSame(# 0
                1# 1
                ,# 2
                %s# 3
                (# 4
                $a# 5
                )# 6
                )# 7
                ;# 8
                EOD),
        ];

        yield [
            self::generateTest('$this->assertCount($b, $a);'),
            self::generateTest('$this->assertSame($b, %s($a));'),
        ];

        yield 'do not fix 1' => [
            self::generateTest('$this->assertSame($b[1], %s($a));'),
        ];

        yield 'do not fix 2' => [
            self::generateTest('$this->assertSame(b(), %s($a));'),
        ];

        yield 'do not fix 3' => [
            self::generateTest('$this->assertSame(1.0, %s($a));'),
        ];

        yield 'do not fix 4' => [
            self::generateTest('$this->assertSame(1);'),
        ];

        yield 'do not fix 5' => [
            self::generateTest('$this->assertSame(1, "%s");'),
        ];

        yield 'do not fix 6' => [
            self::generateTest('$this->test(); // $this->assertSame($b, %s($a));'),
        ];

        yield 'do not fix 7' => [
            self::generateTest('$this->assertSame(2, count($array) - 1);'),
        ];

        yield 'do not fix 8' => [
            self::generateTest(<<<'EOD'

                                    Foo::assertSame(1, sizeof($a));
                                    $this->assertSame(1, sizeof2(2));
                                    $this->assertSame(1, sizeof::foo);
                EOD."\n                "),
        ];
    }

    /**
     * @dataProvider provideTestAssertCountCasingCases
     */
    public function testAssertCountCasing(string $expected, string $input): void
    {
        $expected = sprintf($expected, 'count');
        $input = sprintf($input, 'COUNT');

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideTestAssertCountCasingCases
     */
    public function testAssertCountFromSizeOfCasing(string $expected, string $input): void
    {
        $expected = sprintf($expected, 'sizeof');
        $input = sprintf($input, 'SIZEOF');

        $this->doTest($expected, $input);
    }

    public static function provideTestAssertCountCasingCases(): iterable
    {
        yield [
            self::generateTest('$this->assertCount(1, $a);'),
            self::generateTest('$this->assertSame(1, %s($a));'),
        ];

        yield [
            self::generateTest('$this->assertCount(1, $a);'),
            self::generateTest('$this->assertSame(1, \%s($a));'),
        ];
    }

    /**
     * @dataProvider provideFix73Cases
     */
    public function testFix73(string $expected, string $input): void
    {
        $this->fixer->configure(['target' => PhpUnitTargetVersion::VERSION_NEWEST]);
        $this->doTest($expected, $input);
    }

    public static function provideFix73Cases(): iterable
    {
        yield [
            self::generateTest('$this->assertNan($a, );'),
            self::generateTest('$this->assertTrue(is_nan($a), );'),
        ];

        yield [
            self::generateTest('$this->assertNan($a);'),
            self::generateTest('$this->assertTrue(is_nan($a, ));'),
        ];

        yield [
            self::generateTest('$this->assertNan($a, );'),
            self::generateTest('$this->assertTrue(is_nan($a, ), );'),
        ];

        yield [
            self::generateTest('$this->assertInternalType(\'array\', $a,);'),
            self::generateTest('$this->assertTrue(is_array($a,),);'),
        ];

        yield [
            self::generateTest('$this->assertNan($b);'),
            self::generateTest('$this->assertTrue(\is_nan($b,));'),
        ];

        yield [
            self::generateTest('$this->assertFileExists($f, \'message\',);'),
            self::generateTest('$this->assertTrue(file_exists($f,), \'message\',);'),
        ];

        yield [
            self::generateTest('$this->assertNan($y  , );'),
            self::generateTest('$this->assertTrue(is_nan($y)  , );'),
        ];

        yield 'str_starts_with with trailing ","' => [
            self::generateTest('self::assertStringStartsWith($needle, $haystack);'),
            self::generateTest('self::assertTrue(str_starts_with($haystack, $needle,));'),
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['target' => PhpUnitTargetVersion::VERSION_NEWEST]);
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            self::generateTest('$a = $this->assertTrue(...);'),
        ];
    }

    private static function generateTest(string $content): string
    {
        return "<?php final class FooTest extends \\PHPUnit_Framework_TestCase {\n    public function testSomething() {\n        ".$content."\n    }\n}\n";
    }
}
