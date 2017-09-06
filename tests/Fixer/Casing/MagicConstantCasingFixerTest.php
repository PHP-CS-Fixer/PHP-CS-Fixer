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
        return array(
            array(
                '<?php echo __LINE__;',
                '<?php echo __line__;',
            ),
            array(
                '<?php echo __FILE__;',
                '<?php echo __FILe__;',
            ),
            array(
                '<?php echo __DIR__;',
                '<?php echo __dIr__;',
            ),
            array(
                '<?php echo __FUNCTION__;',
                '<?php echo __fUncTiOn__;',
            ),
            array(
                '<?php echo __CLASS__;',
                '<?php echo __clasS__;',
            ),
            array(
                '<?php echo __METHOD__;',
                '<?php echo __mEthoD__;',
            ),
            array(
                '<?php echo __NAMESPACE__;',
                '<?php echo __namespace__;',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 5.4
     * @dataProvider provideFix54Cases
     */
    public function testFix54($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix54Cases()
    {
        return array(
            array(
                '<?php echo __TRAIT__;',
                '<?php echo __trait__;',
            ),
            array(
                '<?php echo __TRAIT__;',
                '<?php echo __trAIt__;',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 5.5
     * @dataProvider provideFix55Cases
     */
    public function testFix55($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix55Cases()
    {
        return array(
            array(
                '<?php echo Exception::class;',
                '<?php echo Exception::CLASS;',
            ),
            array(
                '<?php echo Exception::class;',
                '<?php echo Exception::ClAss;',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     * @dataProvider provideFix70Cases
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return array(
            array(
                '<?php
                class Bar
                {
                    const __line__ = "foo";
                }

                namespace {
                    echo \Bar::__line__;
                }',
            ),
        );
    }
}
