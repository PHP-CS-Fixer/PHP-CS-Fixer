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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer
 */
final class MagicConstantCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php echo __LINE__;',
                '<?php echo __line__;',
            ],
            [
                '<?php echo __FILE__;',
                '<?php echo __FILe__;',
            ],
            [
                '<?php echo __DIR__;',
                '<?php echo __dIr__;',
            ],
            [
                '<?php echo __FUNCTION__;',
                '<?php echo __fUncTiOn__;',
            ],
            [
                '<?php echo __CLASS__;',
                '<?php echo __clasS__;',
            ],
            [
                '<?php echo __METHOD__;',
                '<?php echo __mEthoD__;',
            ],
            [
                '<?php echo __NAMESPACE__;',
                '<?php echo __namespace__;',
            ],
            [
                '<?php echo __TRAIT__;',
                '<?php echo __trait__;',
            ],
            [
                '<?php echo __TRAIT__;',
                '<?php echo __trAIt__;',
            ],
            [
                '<?php echo Exception::class;',
                '<?php echo Exception::CLASS;',
            ],
            [
                '<?php echo Exception::class;',
                '<?php echo Exception::ClAss;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     * @dataProvider provideFixCases70
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases70()
    {
        return [
            [
                '<?php
                class Bar
                {
                    const __line__ = "foo";
                }

                namespace {
                    echo \Bar::__line__;
                }',
            ],
        ];
    }
}
