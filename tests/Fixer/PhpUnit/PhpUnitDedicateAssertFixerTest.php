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

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer
 */
final class PhpUnitDedicateAssertFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): array
    {
        $cases = [
            [
                self::generateTest('
                    $this->assertNan($a);
                    $this->assertNan($a);
                    $this->assertTrue(test\is_nan($a));
                    $this->assertTrue(test\a\is_nan($a));
                '),
                self::generateTest('
                    $this->assertTrue(is_nan($a));
                    $this->assertTrue(\is_nan($a));
                    $this->assertTrue(test\is_nan($a));
                    $this->assertTrue(test\a\is_nan($a));
                '),
            ],
            [
                self::generateTest('
                    $this->assertFileExists($a);
                    $this->assertFileNotExists($a);
                    $this->assertFileExists($a);
                    $this->assertFileNotExists($a);
                '),
                self::generateTest('
                    $this->assertTrue(file_exists($a));
                    $this->assertFalse(file_exists($a));
                    $this->assertTrue(\file_exists($a));
                    $this->assertFalse(\file_exists($a));
                '),
            ],
            [
                self::generateTest('
                    $this->assertNull($a);
                    $this->assertNotNull($a);
                    $this->assertNull($a);
                    $this->assertNotNull($a, "my message");
                '),
                self::generateTest('
                    $this->assertTrue(is_null($a));
                    $this->assertFalse(is_null($a));
                    $this->assertTrue(\is_null($a));
                    $this->assertFalse(\is_null($a), "my message");
                '),
            ],
            [
                self::generateTest('
                    $this->assertEmpty($a);
                    $this->assertNotEmpty($a);
                '),
                self::generateTest('
                    $this->assertTrue(empty($a));
                    $this->ASSERTFALSE(empty($a));
                '),
            ],
            [
                self::generateTest('
                    $this->assertInfinite($a);
                    $this->assertFinite($a, "my message");
                    $this->assertInfinite($a);
                    $this->assertFinite($a, "my message");
                '),
                self::generateTest('
                    $this->assertTrue(is_infinite($a));
                    $this->assertFalse(is_infinite($a), "my message");
                    $this->assertTrue(\is_infinite($a));
                    $this->assertFalse(\is_infinite($a), "my message");
                '),
            ],
            [
                self::generateTest('
                    $this->assertArrayHasKey("test", $a);
                    $this->assertArrayNotHasKey($b, $a, $c);
                '),
                self::generateTest('
                    $this->assertTrue(\array_key_exists("test", $a));
                    $this->ASSERTFALSE(array_key_exists($b, $a), $c);
                '),
            ],
            [
                self::generateTest('
$this->assertTrue(is_dir($a));
$this->assertTrue(is_writable($a));
$this->assertTrue(is_readable($a));
'),
                null,
                ['target' => PhpUnitTargetVersion::VERSION_5_0],
            ],
            [
                self::generateTest('
$this->assertTrue(is_dir($a));
$this->assertTrue(is_writable($a));
$this->assertTrue(is_readable($a));
'),
                null,
                ['target' => PhpUnitTargetVersion::VERSION_3_0],
            ],
            [
                self::generateTest('
$this->assertDirectoryNotExists($a);
$this->assertNotIsWritable($a);
$this->assertNotIsReadable($a);
'),
                self::generateTest('
$this->assertFalse(is_dir($a));
$this->assertFalse(is_writable($a));
$this->assertFalse(is_readable($a));
'),
                ['target' => PhpUnitTargetVersion::VERSION_5_6],
            ],
            [
                self::generateTest('
$this->assertDirectoryExists($a);
$this->assertIsWritable($a);
$this->assertIsReadable($a);
'),
                self::generateTest('
$this->assertTrue(is_dir($a));
$this->assertTrue(is_writable($a));
$this->assertTrue(is_readable($a));
'),
                ['target' => PhpUnitTargetVersion::VERSION_NEWEST],
            ],
        ];

        foreach (['array', 'bool', 'callable', 'double', 'float', 'int', 'integer', 'long', 'numeric', 'object', 'resource', 'real', 'scalar', 'string'] as $type) {
            $cases[] = [
                self::generateTest(sprintf('$this->assertInternalType(\'%s\', $a);', $type)),
                self::generateTest(sprintf('$this->assertTrue(is_%s($a));', $type)),
            ];

            $cases[] = [
                self::generateTest(sprintf('$this->assertNotInternalType(\'%s\', $a);', $type)),
                self::generateTest(sprintf('$this->assertFalse(is_%s($a));', $type)),
            ];
        }

        $cases[] = [
            self::generateTest('$this->assertInternalType(\'float\', $a, "my message");'),
            self::generateTest('$this->assertTrue(is_float( $a), "my message");'),
        ];

        $cases[] = [
            self::generateTest('$this->assertInternalType(\'float\', $a);'),
            self::generateTest('$this->assertTrue(\IS_FLOAT($a));'),
        ];

        $cases[] = [
            self::generateTest('$this->assertInternalType(#
\'float\'#
, #
$a#
#
)#
;'),
            self::generateTest('$this->assertTrue(#
\IS_FLOAT#
(#
$a#
)#
)#
;'),
        ];

        $cases[] = [
            self::generateTest('static::assertInternalType(\'float\', $a);'),
            self::generateTest('static::assertTrue(is_float( $a));'),
        ];

        $cases[] = [
            self::generateTest('self::assertInternalType(\'float\', $a);'),
            self::generateTest('self::assertTrue(is_float( $a));'),
        ];

        $cases[] = [
            self::generateTest('static::assertNull($a);'),
            self::generateTest('static::assertTrue(is_null($a));'),
        ];

        $cases[] = [
            self::generateTest('self::assertNull($a);'),
            self::generateTest('self::assertTrue(is_null($a));'),
        ];
        $cases[] = [
            self::generateTest('SELF::assertNull($a);'),
            self::generateTest('SELF::assertTrue(is_null($a));'),
        ];

        return $cases;
    }

    /**
     * @dataProvider provideNotFixCases
     */
    public function testNotFix(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideNotFixCases(): array
    {
        return [
            [
                self::generateTest('echo $this->assertTrue;'),
            ],
            [
                self::generateTest('
                    $this->assertTrue(is_null);
                    $this->assertTrue(is_int($a) && $b);
                    $this->assertFalse(is_nan($a));
                    $this->assertTrue(is_int($a) || \is_bool($b));
                    $this->assertTrue($a&&is_int($a));
                    static::assertTrue(is_null);
                    self::assertTrue(is_null);
                '),
            ],
            'not in class' => [
                '<?php self::assertTrue(is_null($a));',
            ],
        ];
    }

    public function testInvalidConfig(): void
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
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

    public function provideTestAssertCountCases(): array
    {
        return [
            // positive fixing
            'assert same' => [
                self::generateTest('$this->assertCount(1, $a);'),
                self::generateTest('$this->assertSame(1, %s($a));'),
            ],
            'assert equals' => [
                self::generateTest('$this->assertCount(2, $b);'),
                self::generateTest('$this->assertEquals(2, %s($b));'),
            ],
            // negative fixing
            'assert not same' => [
                self::generateTest('$this->assertNotCount(11, $c);'),
                self::generateTest('$this->assertNotSame(11, %s($c));'),
            ],
            'assert not equals' => [
                self::generateTest('$this->assertNotCount(122, $d);'),
                self::generateTest('$this->assertNotEquals(122, %s($d));'),
            ],
            // other cases
            'assert same with namespace' => [
                self::generateTest('$this->assertCount(1, $a);'),
                self::generateTest('$this->assertSame(1, \%s($a));'),
            ],
            'no spacing' => [
                self::generateTest('$this->assertCount(1,$a);'),
                self::generateTest('$this->assertSame(1,%s($a));'),
            ],
            'lot of spacing' => [
                self::generateTest('$this->assertCount(
                1
                ,
                '.'
                '.'
                $a
                '.'
                )
                ;'),
                self::generateTest('$this->assertSame(
                1
                ,
                %s
                (
                $a
                )
                )
                ;'),
            ],
            'lot of fix cases' => [
                self::generateTest('
                    $this->assertCount(1, $a);
                    $this->assertCount(2, $a);
                    $this->assertCount(3, $a);
                    $this->assertNotCount(4, $a);
                    $this->assertCount(5, $a, "abc");
                    $this->assertCount(6, $a, "def");
                '),
                self::generateTest('
                    $this->assertSame(1, %1$s($a));
                    $this->assertSame(2, %1$s($a));
                    $this->assertEquals(3, %1$s($a));
                    $this->assertNotSame(4, %1$s($a));
                    $this->assertEquals(5, %1$s($a), "abc");
                    $this->assertSame(6, \%1$s($a), "def");
                '),
            ],
            'comment handling' => [
                self::generateTest('$this->assertCount(# 0
1# 1
,# 2
# 3
# 4
$a# 5
# 6
)# 7
;# 8'),
                self::generateTest('$this->assertSame(# 0
1# 1
,# 2
%s# 3
(# 4
$a# 5
)# 6
)# 7
;# 8'),
            ],
            'do not fix 1' => [
                self::generateTest('$this->assertSame($b, %s($a));'),
            ],
            'do not fix 2' => [
                self::generateTest('$this->assertSame(b(), %s($a));'),
            ],
            'do not fix 3' => [
                self::generateTest('$this->assertSame(1.0, %s($a));'),
            ],
            'do not fix 4' => [
                self::generateTest('$this->assertSame(1);'),
            ],
            'do not fix 5' => [
                self::generateTest('$this->assertSame(1, "%s");'),
            ],
            'do not fix 6' => [
                self::generateTest('$this->test(); // $this->assertSame($b, %s($a));'),
            ],
            'do not fix 7' => [
                self::generateTest('$this->assertSame(2, count($array) - 1);'),
            ],
            'do not fix 8' => [
                self::generateTest('
                    Foo::assertSame(1, sizeof($a));
                    $this->assertSame(1, sizeof2(2));
                    $this->assertSame(1, sizeof::foo);
                '),
            ],
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

    public function provideTestAssertCountCasingCases(): array
    {
        return [
            [
                self::generateTest('$this->assertCount(1, $a);'),
                self::generateTest('$this->assertSame(1, %s($a));'),
            ],
            [
                self::generateTest('$this->assertCount(1, $a);'),
                self::generateTest('$this->assertSame(1, \%s($a));'),
            ],
        ];
    }

    /**
     * @requires PHP 7.3
     * @dataProvider provideFix73Cases
     */
    public function testFix73(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix73Cases(): array
    {
        return [
            [
                self::generateTest('$this->assertNan($a, );'),
                self::generateTest('$this->assertTrue(is_nan($a), );'),
            ],
            [
                self::generateTest('$this->assertNan($a);'),
                self::generateTest('$this->assertTrue(is_nan($a, ));'),
            ],
            [
                self::generateTest('$this->assertNan($a, );'),
                self::generateTest('$this->assertTrue(is_nan($a, ), );'),
            ],
            [
                self::generateTest('$this->assertInternalType(\'array\', $a,);'),
                self::generateTest('$this->assertTrue(is_array($a,),);'),
            ],
            [
                self::generateTest('$this->assertNan($b);'),
                self::generateTest('$this->assertTrue(\is_nan($b,));'),
            ],
            [
                self::generateTest('$this->assertFileExists($f, \'message\',);'),
                self::generateTest('$this->assertTrue(file_exists($f,), \'message\',);'),
            ],
            [
                self::generateTest('$this->assertNan($y  , );'),
                self::generateTest('$this->assertTrue(is_nan($y)  , );'),
            ],
        ];
    }

    private static function generateTest(string $content): string
    {
        return "<?php final class FooTest extends \\PHPUnit_Framework_TestCase {\n    public function testSomething() {\n        ".$content."\n    }\n}\n";
    }
}
