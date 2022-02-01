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

namespace PhpCsFixer\Tests\Fixer\CastNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\CastNotation\CastSpacesFixer
 */
final class CastSpacesFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigMissingKey(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[cast_spaces\] Invalid configuration: The option "a" does not exist\. Defined options are: "space"\.$#');

        $this->fixer->configure(['a' => 1]);
    }

    public function testInvalidConfigValue(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[cast_spaces\] Invalid configuration: The option "space" with value "double" is invalid\. Accepted values are: "none", "single"\.$#');

        $this->fixer->configure(['space' => 'double']);
    }

    /**
     * @dataProvider provideFixCastsCases
     */
    public function testFixCastsWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCastsCases
     */
    public function testFixCastsSingleSpace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['space' => 'single']);
        $this->doTest($expected, $input);
    }

    public function provideFixCastsCases(): array
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
     * @dataProvider provideNoneSpaceFixCases
     */
    public function testFixCastsNoneSpace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['space' => 'none']);
        $this->doTest($expected, $input);
    }

    public function provideNoneSpaceFixCases(): array
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
