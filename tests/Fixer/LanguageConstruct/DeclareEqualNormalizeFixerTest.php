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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer
 */
final class DeclareEqualNormalizeFixerTest extends AbstractFixerTestCase
{
    /**
     * @group legacy
     * @expectedDeprecation Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.
     */
    public function testLegacyFix()
    {
        $this->fixer->configure(null);
        $this->doTest(
             '<?php declare(ticks=1);',
            '<?php declare(ticks= 1);'
        );
    }

    /**
     * @param string $expected
     * @param string $input
     * @param array  $config
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input, array $config)
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideCases()
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
        ];
    }

    /**
     * @param array  $config
     * @param string $expectedMessage
     *
     * @dataProvider provideInvalidConfig
     */
    public function testInvalidConfig(array $config, $expectedMessage)
    {
        $this->setExpectedException(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            sprintf('[declare_equal_normalize] Invalid configuration: %s', $expectedMessage)
        );

        $this->fixer->configure($config);
    }

    public function provideInvalidConfig()
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
