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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
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
        return array(
            'minimal case remove whitespace (default config)' => array(
                '<?php declare(ticks=1);',
                '<?php declare(ticks= 1);',
                array(),
            ),
            'minimal case remove whitespace (no space config)' => array(
                '<?php declare(ticks=1);',
                '<?php declare(ticks  =  1);',
                array('space' => 'none'),
            ),
            'minimal case add whitespace' => array(
                '<?php declare(ticks = 1);',
                '<?php declare(ticks=1);',
                array('space' => 'single'),
            ),
            'to much whitespace case add whitespace' => array(
                '<?php declare(ticks = 1);',
                "<?php declare(ticks\n\t =   1);",
                array('space' => 'single'),
            ),
            'repeating case remove whitespace (default config)' => array(
                '<?php declare(ticks=1);declare(ticks=1)?>',
                '<?php declare(ticks= 1);declare(ticks= 1)?>',
                array(),
            ),
            'repeating case add whitespace' => array(
                '<?php declare ( ticks = 1 );declare( ticks = 1)  ?>',
                '<?php declare ( ticks=1 );declare( ticks =1)  ?>',
                array('space' => 'single'),
            ),
            'minimal case add whitespace comments, single' => array(
                '<?php declare(ticks#
= #
1#
);',
                '<?php declare(ticks#
=#
1#
);',
                array('space' => 'single'),
            ),
            'minimal case add whitespace comments, none' => array(
                '<?php declare(ticks#
=#
1#
);',
                null,
                array('space' => 'none'),
            ),
        );
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
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            sprintf('[declare_equal_normalize] Invalid configuration: %s', $expectedMessage)
        );

        $this->fixer->configure($config);
    }

    public function provideInvalidConfig()
    {
        return array(
            array(
                array(1, 2),
                'The options "0", "1" do not exist.',
            ),
            array(
                array('space' => 'tab'),
                'The option "space" with value "tab" is invalid.',
            ),
        );
    }
}
