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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
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
     * @requires PHP <8.0
     *
     * @dataProvider provideFix74Cases
     */
    public function testFix74(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix74Cases(): array
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
