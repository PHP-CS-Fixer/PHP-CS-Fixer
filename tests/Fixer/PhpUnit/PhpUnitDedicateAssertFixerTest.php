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
     * @param array       $config
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixLegacyCases
     * @group legacy
     * @expectedDeprecation Option "functions" is deprecated and will be removed in 3.0. Use option "target" instead.
     */
    public function testFixLegacy($expected, $input = null)
    {
        $defaultFunctions = [
            'array_key_exists',
            'empty',
            'file_exists',
            'is_array',
            'is_bool',
            'is_callable',
            'is_double',
            'is_float',
            'is_infinite',
            'is_int',
            'is_integer',
            'is_long',
            'is_nan',
            'is_null',
            'is_numeric',
            'is_object',
            'is_real',
            'is_resource',
            'is_scalar',
            'is_string',
        ];

        $this->fixer->configure(['functions' => $defaultFunctions]);
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

        return $cases;
    }

    public function provideTestFixLegacyCases()
    {
        return array_filter($this->provideTestFixCases(), function (array $case) { return !isset($case[2]); });
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

    /**
     * @group legacy
     * @expectedDeprecation Passing "functions" at the root of the configuration is deprecated and will not be supported in 3.0, use "functions" => array(...) option instead.
     */
    public function testLegacyConfig()
    {
        $this->fixer->configure(['file_exists']);
        $this->doTest(
            '<?php
                    $this->assertFileExists($a);
                    $this->assertTrue(is_infinite($a));
            ',
            '<?php
                    $this->assertTrue(file_exists($a));
                    $this->assertTrue(is_infinite($a));
            '
        );
    }

    /**
     * @group legacy
     * @expectedDeprecation Option "functions" is deprecated and will be removed in 3.0. Use option "target" instead.
     */
    public function testConfig()
    {
        $this->fixer->configure(['functions' => ['file_exists']]);
        $this->doTest(
            '<?php
                    $this->assertFileExists($a);
                    $this->assertTrue(is_infinite($a));
            ',
            '<?php
                    $this->assertTrue(file_exists($a));
                    $this->assertTrue(is_infinite($a));
            '
        );
    }

    public function testInvalidConfig()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[php_unit_dedicate_assert\] Invalid configuration: The option "target" .*\.$/');

        $this->fixer->configure(['target' => '_unknown_']);
    }
}
