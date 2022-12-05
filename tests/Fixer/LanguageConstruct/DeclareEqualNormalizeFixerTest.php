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

    public static function provideFixCases(): array
    {
        return [
            'minimal case remove whitespace (default config)' => [
                '<?php declare(ticks=1);',
                '<?php declare(ticks= 1);',
                [],
            ],
            'minimal case remove whitespace (no space config)' => [
                '<?php declare(ticks=1);',
                '<?php declare(ticks  =  1);',
                ['space' => 'none'],
            ],
            'minimal case add whitespace' => [
                '<?php declare(ticks = 1);',
                '<?php declare(ticks=1);',
                ['space' => 'single'],
            ],
            'to much whitespace case add whitespace' => [
                '<?php declare(ticks = 1);',
                "<?php declare(ticks\n\t =   1);",
                ['space' => 'single'],
            ],
            'repeating case remove whitespace (default config)' => [
                '<?php declare(ticks=1);declare(ticks=1)?>',
                '<?php declare(ticks= 1);declare(ticks= 1)?>',
                [],
            ],
            'repeating case add whitespace' => [
                '<?php declare ( ticks = 1 );declare( ticks = 1)  ?>',
                '<?php declare ( ticks=1 );declare( ticks =1)  ?>',
                ['space' => 'single'],
            ],
            'minimal case add whitespace comments, single' => [
                '<?php declare(ticks#
= #
1#
);',
                '<?php declare(ticks#
=#
1#
);',
                ['space' => 'single'],
            ],
            'minimal case add whitespace comments, none' => [
                '<?php declare(ticks#
=#
1#
);',
                null,
                ['space' => 'none'],
            ],
            'declare having multiple directives, single' => [
                '<?php declare(strict_types=1, ticks=1);',
                '<?php declare(strict_types = 1, ticks = 1);',
                [],
            ],
            'declare having multiple directives, none' => [
                '<?php declare(strict_types = 1, ticks = 1);',
                '<?php declare(strict_types=1, ticks=1);',
                ['space' => 'single'],
            ],
        ];
    }

    /**
     * @param array<mixed> $config
     *
     * @dataProvider provideInvalidConfigCases
     */
    public function testInvalidConfig(array $config, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage(sprintf('[declare_equal_normalize] Invalid configuration: %s', $expectedMessage));

        $this->fixer->configure($config);
    }

    public static function provideInvalidConfigCases(): array
    {
        return [
            [
                [1, 2],
                'The options "0", "1" do not exist.',
            ],
            [
                ['space' => 'tab'],
                'The option "space" with value "tab" is invalid.',
            ],
        ];
    }
}
