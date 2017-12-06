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

namespace PhpCsFixer\Tests\Fixer\CastNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\CastNotation\CastSpacesFixer
 */
final class CastSpacesFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigMissingKey()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('#^\[cast_spaces\] Invalid configuration: The option "a" does not exist\. Defined options are: "space"\.$#');

        $this->fixer->configure(['a' => 1]);
    }

    public function testInvalidConfigValue()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('#^\[cast_spaces\] Invalid configuration: The option "space" with value "double" is invalid\. Accepted values are: "none", "single"\.$#');

        $this->fixer->configure(['space' => 'double']);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCastsCases
     */
    public function testFixCastsWithDefaultConfiguration($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCastsCases
     */
    public function testFixCastsSingleSpace($expected, $input = null)
    {
        $this->fixer->configure(['space' => 'single']);
        $this->doTest($expected, $input);
    }

    public function provideFixCastsCases()
    {
        return [
            [
                '<?php echo "( int ) $foo";',
            ],
            [
                '<?php $bar = (int) $foo;',
                '<?php $bar = ( int)$foo;',
            ],
            [
                '<?php $bar = (int) $foo;',
                '<?php $bar = (	int)$foo;',
            ],
            [
                '<?php $bar = (int) $foo;',
                '<?php $bar = (int)	$foo;',
            ],
            [
                '<?php $bar = (string) (int) $foo;',
                '<?php $bar = ( string )( int )$foo;',
            ],
            [
                '<?php $bar = (string) (int) $foo;',
                '<?php $bar = (string)(int)$foo;',
            ],
            [
                '<?php $bar = (string) (int) $foo;',
                '<?php $bar = ( string   )    (   int )$foo;',
            ],
            [
                '<?php $bar = (string) $foo;',
                '<?php $bar = ( string )   $foo;',
            ],
            [
                '<?php $bar = (float) Foo::bar();',
                '<?php $bar = (float )Foo::bar();',
            ],
            [
                '<?php $bar = Foo::baz((float) Foo::bar());',
                '<?php $bar = Foo::baz((float )Foo::bar());',
            ],
            [
                '<?php $bar = $query["params"] = (array) $query["params"];',
                '<?php $bar = $query["params"] = (array)$query["params"];',
            ],
            [
                "<?php \$bar = (int)\n \$foo;",
            ],
            [
                "<?php \$bar = (int)\r\n \$foo;",
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideNoneSpaceFixCases
     */
    public function testFixCastsNoneSpace($expected, $input = null)
    {
        $this->fixer->configure(['space' => 'none']);
        $this->doTest($expected, $input);
    }

    public function provideNoneSpaceFixCases()
    {
        return [
            [
                '<?php echo "( int ) $foo";',
            ],
            [
                '<?php $bar = (int)$foo;',
                '<?php $bar = ( int)$foo;',
            ],
            [
                '<?php $bar = (int)$foo;',
                '<?php $bar = (	int)$foo;',
            ],
            [
                '<?php $bar = (int)$foo;',
                '<?php $bar = (int)	$foo;',
            ],
            [
                '<?php $bar = (string)(int)$foo;',
                '<?php $bar = ( string )( int )$foo;',
            ],
            [
                '<?php $bar = (string)(int)$foo;',
            ],
            [
                '<?php $bar = (string)(int)$foo;',
                '<?php $bar = ( string   )    (   int )$foo;',
            ],
            [
                '<?php $bar = (string)$foo;',
                '<?php $bar = ( string )   $foo;',
            ],
            [
                '<?php $bar = (float)Foo::bar();',
                '<?php $bar = (float )Foo::bar();',
            ],
            [
                '<?php $bar = Foo::baz((float)Foo::bar());',
                '<?php $bar = Foo::baz((float )Foo::bar());',
            ],
            [
                '<?php $bar = $query["params"] = (array)$query["params"];',
            ],
            [
                '<?php $bar = (int)$foo;',
                "<?php \$bar = (int)\n \$foo;",
            ],
            [
                '<?php $bar = (int)$foo;',
                "<?php \$bar = (int)\r\n \$foo;",
            ],
        ];
    }
}
