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
    /**
     * @param array<string, int|string> $config
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $config, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        $this->fixer->configure($config);
    }

    /**
     * @return iterable<array{array<string, int|string>, string}>
     */
    public static function provideInvalidConfigurationCases(): iterable
    {
        yield 'missing key' => [
            ['a' => 1],
            '#^\[cast_spaces\] Invalid configuration: The option "a" does not exist\. Defined options are: "space"\.$#',
        ];

        yield 'invalid value' => [
            ['space' => 'double'],
            '#^\[cast_spaces\] Invalid configuration: The option "space" with value "double" is invalid\. Accepted values are: "none", "single"\.$#',
        ];
    }

    /**
     * @param array<string, string> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: null|string, 2?: array{space: string}}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php echo "( int ) $foo";',
        ];

        yield [
            '<?php $bar = (int) $foo;',
            '<?php $bar = ( int)$foo;',
        ];

        yield [
            '<?php $bar = (int) $foo;',
            '<?php $bar = (	int)$foo;',
        ];

        yield [
            '<?php $bar = (int) $foo;',
            '<?php $bar = (int)	$foo;',
        ];

        yield [
            '<?php $bar = (string) (int) $foo;',
            '<?php $bar = ( string )( int )$foo;',
        ];

        yield [
            '<?php $bar = (string) (int) $foo;',
            '<?php $bar = (string)(int)$foo;',
        ];

        yield [
            '<?php $bar = (string) (int) $foo;',
            '<?php $bar = ( string   )    (   int )$foo;',
        ];

        yield [
            '<?php $bar = (string) $foo;',
            '<?php $bar = ( string )   $foo;',
        ];

        yield [
            '<?php $bar = (float) Foo::bar();',
            '<?php $bar = (float )Foo::bar();',
        ];

        yield [
            '<?php $bar = Foo::baz((float) Foo::bar());',
            '<?php $bar = Foo::baz((float )Foo::bar());',
        ];

        yield [
            '<?php $bar = $query["params"] = (array) $query["params"];',
            '<?php $bar = $query["params"] = (array)$query["params"];',
        ];

        yield [
            "<?php \$bar = (int)\n \$foo;",
        ];

        yield [
            "<?php \$bar = (int)\r\n \$foo;",
        ];

        yield [
            '<?php echo "( int ) $foo";',
            null,
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (int)$foo;',
            '<?php $bar = ( int)$foo;',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (int)$foo;',
            '<?php $bar = (	int)$foo;',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (int)$foo;',
            '<?php $bar = (int)	$foo;',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (string)(int)$foo;',
            '<?php $bar = ( string )( int )$foo;',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (string)(int)$foo;',
            null,
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (string)(int)$foo;',
            '<?php $bar = ( string   )    (   int )$foo;',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (string)$foo;',
            '<?php $bar = ( string )   $foo;',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (float)Foo::bar();',
            '<?php $bar = (float )Foo::bar();',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = Foo::baz((float)Foo::bar());',
            '<?php $bar = Foo::baz((float )Foo::bar());',
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = $query["params"] = (array)$query["params"];',
            null,
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (int)$foo;',
            "<?php \$bar = (int)\n \$foo;",
            ['space' => 'none'],
        ];

        yield [
            '<?php $bar = (int)$foo;',
            "<?php \$bar = (int)\r\n \$foo;",
            ['space' => 'none'],
        ];
    }
}
