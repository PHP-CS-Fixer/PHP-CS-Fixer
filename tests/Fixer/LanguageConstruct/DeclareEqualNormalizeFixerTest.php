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
     * @param string     $expected
     * @param string     $input
     * @param array|null $config
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input, $config)
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
                null,
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
                null,
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
     * @dataProvider provideInvalidConfig
     */
    public function testInvalidConfig(array $config)
    {
        $this->setExpectedException(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '[declare_equal_normalize] Configuration must define "space" being "single" or "none".'
        );

        $this->fixer->configure($config);
    }

    public function provideInvalidConfig()
    {
        return array(
            array(array()),
            array(array(1, 2)),
            array(array('space' => 'tab')),
        );
    }
}
