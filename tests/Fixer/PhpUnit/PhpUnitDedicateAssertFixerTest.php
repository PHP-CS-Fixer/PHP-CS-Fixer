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
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer
 */
final class PhpUnitDedicateAssertFixerTest extends AbstractFixerTestCase
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
        $cases = [
            [
                '<?php
                    $this->assertNan($a);
                    $this->assertNan($a);
                    $this->assertTrue(test\is_nan($a));
                    $this->assertTrue(test\a\is_nan($a));
                ',
                '<?php
                    $this->assertTrue(is_nan($a));
                    $this->assertTrue(\is_nan($a));
                    $this->assertTrue(test\is_nan($a));
                    $this->assertTrue(test\a\is_nan($a));
                ',
            ],
            [
                '<?php
                    $this->assertFileExists($a);
                    $this->assertFileNotExists($a);
                    $this->assertFileExists($a);
                    $this->assertFileNotExists($a);
                ',
                '<?php
                    $this->assertTrue(file_exists($a));
                    $this->assertFalse(file_exists($a));
                    $this->assertTrue(\file_exists($a));
                    $this->assertFalse(\file_exists($a));
                ',
            ],
            [
                '<?php
                    $this->assertNull($a);
                    $this->assertNotNull($a);
                    $this->assertNull($a);
                    $this->assertNotNull($a, "my message");
                ',
                '<?php
                    $this->assertTrue(is_null($a));
                    $this->assertFalse(is_null($a));
                    $this->assertTrue(\is_null($a));
                    $this->assertFalse(\is_null($a), "my message");
                ',
            ],
            [
                '<?php
                    $this->assertEmpty($a);
                    $this->assertNotEmpty($a);
                ',
                '<?php
                    $this->assertTrue(empty($a));
                    $this->ASSERTFALSE(empty($a));
                ',
            ],
            [
                '<?php
                    $this->assertInfinite($a);
                    $this->assertFinite($a, "my message");
                    $this->assertInfinite($a);
                    $this->assertFinite($a, "my message");
                ',
                '<?php
                    $this->assertTrue(is_infinite($a));
                    $this->assertFalse(is_infinite($a), "my message");
                    $this->assertTrue(\is_infinite($a));
                    $this->assertFalse(\is_infinite($a), "my message");
                ',
            ],
            [
                '<?php
                    $this->assertArrayHasKey("test", $a);
                    $this->assertArrayNotHasKey($b, $a, $c);
                ',
                '<?php
                    $this->assertTrue(\array_key_exists("test", $a));
                    $this->ASSERTFALSE(array_key_exists($b, $a), $c);
                ',
            ],
            [
                '<?php
$this->assertTrue(is_dir($a));
$this->assertTrue(is_writable($a));
$this->assertTrue(is_readable($a));
',
                null,
                ['target' => PhpUnitTargetVersion::VERSION_5_0],
            ],
            [
                '<?php
$this->assertTrue(is_dir($a));
$this->assertTrue(is_writable($a));
$this->assertTrue(is_readable($a));
',
                null,
                ['target' => PhpUnitTargetVersion::VERSION_3_0],
            ],
            [
                '<?php
$this->assertDirectoryNotExists($a);
$this->assertNotIsWritable($a);
$this->assertNotIsReadable($a);
',
                '<?php
$this->assertFalse(is_dir($a));
$this->assertFalse(is_writable($a));
$this->assertFalse(is_readable($a));
',
                ['target' => PhpUnitTargetVersion::VERSION_5_6],
            ],
            [
                '<?php
$this->assertDirectoryExists($a);
$this->assertIsWritable($a);
$this->assertIsReadable($a);
',
                '<?php
$this->assertTrue(is_dir($a));
$this->assertTrue(is_writable($a));
$this->assertTrue(is_readable($a));
',
                ['target' => PhpUnitTargetVersion::VERSION_NEWEST],
            ],
        ];

        foreach (['array', 'bool', 'callable', 'double', 'float', 'int', 'integer', 'long', 'numeric', 'object', 'resource', 'real', 'scalar', 'string'] as $type) {
            $cases[] = [
                sprintf('<?php $this->assertInternalType(\'%s\', $a);', $type),
                sprintf('<?php $this->assertTrue(is_%s($a));', $type),
            ];

            $cases[] = [
                sprintf('<?php $this->assertNotInternalType(\'%s\', $a);', $type),
                sprintf('<?php $this->assertFalse(is_%s($a));', $type),
            ];
        }

        $cases[] = [
            '<?php $this->assertInternalType(\'float\', $a, "my message");',
            '<?php $this->assertTrue(is_float( $a), "my message");',
        ];

        $cases[] = [
            '<?php $this->assertInternalType(\'float\', $a);',
            '<?php $this->assertTrue(\IS_FLOAT($a));',
        ];

        $cases[] = [
            '<?php $this->assertInternalType(#
\'float\'#
, #
$a#
#
)#
;',
            '<?php $this->assertTrue(#
\IS_FLOAT#
(#
$a#
)#
)#
;',
        ];

        $cases[] = [
            '<?php static::assertInternalType(\'float\', $a);',
            '<?php static::assertTrue(is_float( $a));',
        ];

        $cases[] = [
            '<?php self::assertInternalType(\'float\', $a);',
            '<?php self::assertTrue(is_float( $a));',
        ];

        $cases[] = [
            '<?php static::assertNull($a);',
            '<?php static::assertTrue(is_null($a));',
        ];

        $cases[] = [
            '<?php self::assertNull($a);',
            '<?php self::assertTrue(is_null($a));',
        ];
        $cases[] = [
            '<?php SELF::assertNull($a);',
            '<?php SELF::assertTrue(is_null($a));',
        ];

        return $cases;
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideNotFixCases
     */
    public function testNotFix($expected)
    {
        $this->doTest($expected);
    }

    public function provideNotFixCases()
    {
        return [
            [
                '<?php echo $this->assertTrue;',
            ],
            [
                '<?php echo $this->assertTrue?>',
            ],
            [
                '<?php
                    const is_null = 1;
                    $this->assertTrue(is_null);
                    $this->assertTrue(is_int($a) && $b);
                    $this->assertFalse(is_nan($a));
                    $this->assertTrue(is_int($a) || \is_bool($b));
                    $this->assertTrue($a&&is_int($a));
                    static::assertTrue(is_null);
                    self::assertTrue(is_null);
                ',
            ],
        ];
    }

    public function testInvalidConfig()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[php_unit_dedicate_assert\] Invalid configuration: The option "target" .*\.$/');

        $this->fixer->configure(['target' => '_unknown_']);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestAssertCountCases
     */
    public function testAssertCount($expected, $input = null)
    {
        if (null === $input) {
            $expected = sprintf($expected, 'count');
        } else {
            $input = sprintf($input, 'count');
        }

        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestAssertCountCases
     */
    public function testAssertCountFromSizeOf($expected, $input = null)
    {
        if (null === $input) {
            $expected = sprintf($expected, 'sizeof');
        } else {
            $input = sprintf($input, 'sizeof');
        }

        $this->doTest($expected, $input);
    }

    public function provideTestAssertCountCases()
    {
        return [
            // positive fixing
            'assert same' => [
                '<?php $this->assertCount(1, $a);',
                '<?php $this->assertSame(1, %s($a));',
            ],
            'assert equals' => [
                '<?php $this->assertCount(2, $b);',
                '<?php $this->assertEquals(2, %s($b));',
            ],
            // negative fixing
            'assert not same' => [
                '<?php $this->assertNotCount(11, $c);',
                '<?php $this->assertNotSame(11, %s($c));',
            ],
            'assert not equals' => [
                '<?php $this->assertNotCount(122, $d);',
                '<?php $this->assertNotEquals(122, %s($d));',
            ],
            // other cases
            'assert same with namespace' => [
                '<?php $this->assertCount(1, $a);',
                '<?php $this->assertSame(1, \%s($a));',
            ],
            'no spacing' => [
                '<?php $this->assertCount(1,$a);',
                '<?php $this->assertSame(1,%s($a));',
            ],
            'lot of spacing' => [
                '<?php $this->assertCount(
                1
                ,
                '.'
                '.'
                $a
                '.'
                )
                ;',
                '<?php $this->assertSame(
                1
                ,
                %s
                (
                $a
                )
                )
                ;',
            ],
            'lot of fix cases' => [
                '<?php
                    $this->assertCount(1, $a);
                    $this->assertCount(2, $a);
                    $this->assertCount(3, $a);
                    $this->assertNotCount(4, $a);
                    $this->assertCount(5, $a, "abc");
                    $this->assertCount(6, $a, "def");
                ',
                '<?php
                    $this->assertSame(1, %1$s($a));
                    $this->assertSame(2, %1$s($a));
                    $this->assertEquals(3, %1$s($a));
                    $this->assertNotSame(4, %1$s($a));
                    $this->assertEquals(5, %1$s($a), "abc");
                    $this->assertSame(6, \%1$s($a), "def");
                ',
            ],
            'comment handling' => [
                '<?php $this->assertCount(# 0
1# 1
,# 2
# 3
# 4
$a# 5
# 6
)# 7
;# 8',
                '<?php $this->assertSame(# 0
1# 1
,# 2
%s# 3
(# 4
$a# 5
)# 6
)# 7
;# 8',
            ],
            'do not fix 1' => [
                '<?php
                    $this->assertSame($b, %s($a));
                ',
            ],
            'do not fix 2' => [
                '<?php
                    $this->assertSame(b(), %s($a));
                ',
            ],
            'do not fix 3' => [
                '<?php
                    $this->assertSame(1.0, %s($a));
                ',
            ],
            'do not fix 4' => [
                '<?php
                    $this->assertSame(1);
                ',
            ],
            'do not fix 5' => [
                '<?php
                    $this->assertSame(1, "%s");
                ',
            ],
            'do not fix 6' => [
                '<?php
                    $this->test(); // $this->assertSame($b, %s($a));
                ',
            ],
            'do not fix 7' => [
                '<?php
                    $this->assertSame(2, count($array) - 1);
                ',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideTestAssertCountCasingCases
     */
    public function testAssertCountCasing($expected, $input)
    {
        $expected = sprintf($expected, 'count');
        $input = sprintf($input, 'COUNT');

        $this->doTest($expected, $input);
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideTestAssertCountCasingCases
     */
    public function testAssertCountFromSizeOfCasing($expected, $input)
    {
        $expected = sprintf($expected, 'sizeof');
        $input = sprintf($input, 'SIZEOF');

        $this->doTest($expected, $input);
    }

    public function provideTestAssertCountCasingCases()
    {
        return [
            [
                '<?php $this->assertCount(1, $a);',
                '<?php $this->assertSame(1, %s($a));',
            ],
            [
                '<?php $this->assertCount(1, $a);',
                '<?php $this->assertSame(1, \%s($a));',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @requires PHP 7.3
     * @dataProvider provideFix73Cases
     */
    public function testFix73($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix73Cases()
    {
        return [
            [
                '<?php $this->assertNan($a, );',
                '<?php $this->assertTrue(is_nan($a), );',
            ],
            [
                '<?php $this->assertNan($a);',
                '<?php $this->assertTrue(is_nan($a, ));',
            ],
            [
                '<?php $this->assertNan($a, );',
                '<?php $this->assertTrue(is_nan($a, ), );',
            ],
            [
                '<?php $this->assertInternalType(\'array\', $a,);',
                '<?php $this->assertTrue(is_array($a,),);',
            ],
            [
                '<?php
                   $this->assertNan($b);
               ',
                '<?php
                   $this->assertTrue(\is_nan($b,));
               ',
            ],
            [
                '<?php
                   $this->assertFileExists($f, \'message\',);
               ',
                '<?php
                   $this->assertTrue(file_exists($f,), \'message\',);
               ',
            ],
            [
                '<?php $this->assertNan($y  , );',
                '<?php $this->assertTrue(is_nan($y)  , );',
            ],
        ];
    }
}
