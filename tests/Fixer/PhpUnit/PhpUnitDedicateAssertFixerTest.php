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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class PhpUnitDedicateAssertFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideInternalTypeMethods
     */
    public function testInternalTypeMethods($expected, $input = null)
    {
        $this->doTest($expected, $input);

        $defaultFunctions = array(
            'array_key_exists',
            'empty',
            'file_exists',
            'is_infinite',
            'is_nan',
            'is_null',
            'is_array',
            'is_bool',
            'is_boolean',
            'is_callable',
            'is_double',
            'is_float',
            'is_int',
            'is_integer',
            'is_long',
            'is_numeric',
            'is_object',
            'is_real',
            'is_resource',
            'is_scalar',
            'is_string',
        );

        $this->fixer->configure(array('functions' => $defaultFunctions));
        $this->doTest($expected, $input);
    }

    public function provideInternalTypeMethods()
    {
        $cases = array();

        foreach (array('array', 'bool', 'boolean', 'callable', 'double', 'float', 'int', 'integer', 'long', 'numeric', 'object', 'resource', 'real', 'scalar', 'string') as $type) {
            $cases[] = array(
                sprintf('<?php $this->assertInternalType(\'%s\', $a);', $type),
                sprintf('<?php $this->assertTrue(is_%s($a));', $type),
            );

            $cases[] = array(
                sprintf('<?php $this->assertNotInternalType(\'%s\', $a);', $type),
                sprintf('<?php $this->assertFalse(is_%s($a));', $type),
            );
        }

        $cases[] = array(
            '<?php $this->assertInternalType(\'float\', $a, "my message");',
            '<?php $this->assertTrue(is_float( $a), "my message");',
        );

        $cases[] = array(
            '<?php $this->assertInternalType(\'float\', $a);',
            '<?php $this->assertTrue(\IS_FLOAT($a));',
        );

        $cases[] = array(
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
        );

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideDedicatedAssertsCases
     */
    public function testDedicatedAsserts($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideDedicatedAssertsCases()
    {
        return array(
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
            array(
                '<?php
                    $this->assertEmpty($a);
                    $this->assertNotEmpty($a);
                ',
                '<?php
                    $this->assertTrue(empty($a));
                    $this->ASSERTFALSE(empty($a));
                ',
            ),
            array(
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
            ),
            array(
                '<?php
                    $this->assertArrayHasKey("test", $a);
                    $this->assertArrayNotHasKey($b, $a, $c);
                ',
                '<?php
                    $this->assertTrue(\array_key_exists("test", $a));
                    $this->ASSERTFALSE(array_key_exists($b, $a), $c);
                ',
            ),
        );
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
        return array(
            array(
                '<?php echo $this->assertTrue;',
            ),
            array(
                '<?php echo $this->assertTrue?>',
            ),
            array(
                '<?php
                    const is_null = 1;
                    $this->assertTrue(is_null);
                    $this->assertTrue(is_int($a) && $b);
                    $this->assertFalse(is_nan($a));
                    $this->assertTrue(is_int($a) || \is_bool($b));
                    $this->assertTrue($a&&is_int($a));
                ',
            ),
        );
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing "functions" at the root of the configuration is deprecated and will not be supported in 3.0, use "functions" => array(...) option instead.
     */
    public function testLegacyConfig()
    {
        $this->fixer->configure(array('file_exists'));
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

    public function testConfig()
    {
        $this->fixer->configure(array('functions' => array('file_exists')));
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
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[php_unit_dedicate_assert\] Invalid configuration: The option "functions" .*\.$/'
        );

        $this->fixer->configure(array('functions' => array('_unknown_')));
    }
}
