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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer
 */
final class DeclareEqualNormalizeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input, array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'minimal case remove whitespace (default config)' => [
            '<?php declare(ticks=1);',
            '<?php declare(ticks= 1);',
            [],
        ];

        yield 'minimal case remove whitespace (no space config)' => [
            '<?php declare(ticks=1);',
            '<?php declare(ticks  =  1);',
            ['space' => 'none'],
        ];

        yield 'minimal case add whitespace' => [
            '<?php declare(ticks = 1);',
            '<?php declare(ticks=1);',
            ['space' => 'single'],
        ];

        yield 'to much whitespace case add whitespace' => [
            '<?php declare(ticks = 1);',
            "<?php declare(ticks\n\t =   1);",
            ['space' => 'single'],
        ];

        yield 'repeating case remove whitespace (default config)' => [
            '<?php declare(ticks=1);declare(ticks=1)?>',
            '<?php declare(ticks= 1);declare(ticks= 1)?>',
            [],
        ];

        yield 'repeating case add whitespace' => [
            '<?php declare ( ticks = 1 );declare( ticks = 1)  ?>',
            '<?php declare ( ticks=1 );declare( ticks =1)  ?>',
            ['space' => 'single'],
        ];

        yield 'minimal case add whitespace comments, single' => [
            '<?php declare(ticks#
= #
1#
);',
            '<?php declare(ticks#
=#
1#
);',
            ['space' => 'single'],
        ];

        yield 'minimal case add whitespace comments, none' => [
            '<?php declare(ticks#
=#
1#
);',
            null,
            ['space' => 'none'],
        ];

        yield 'declare having multiple directives, single' => [
            '<?php declare(strict_types=1, ticks=1);',
            '<?php declare(strict_types = 1, ticks = 1);',
            [],
        ];

        yield 'declare having multiple directives, none' => [
            '<?php declare(strict_types = 1, ticks = 1);',
            '<?php declare(strict_types=1, ticks=1);',
            ['space' => 'single'],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $config, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage(sprintf('[declare_equal_normalize] Invalid configuration: %s', $expectedMessage));

        $this->fixer->configure($config);
    }

    public static function provideInvalidConfigurationCases(): iterable
    {
        yield [
            [1, 2],
            'The options "0", "1" do not exist.',
        ];

        yield [
            ['space' => 'tab'],
            'The option "space" with value "tab" is invalid.',
        ];
    }
}
