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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer>
 *
 * @author ntzm
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php echo __LINE__;',
            '<?php echo __line__;',
        ];

        yield [
            '<?php echo __FILE__;',
            '<?php echo __FILe__;',
        ];

        yield [
            '<?php echo __DIR__;',
            '<?php echo __dIr__;',
        ];

        yield [
            '<?php echo __FUNCTION__;',
            '<?php echo __fUncTiOn__;',
        ];

        yield [
            '<?php echo __CLASS__;',
            '<?php echo __clasS__;',
        ];

        yield [
            '<?php echo __METHOD__;',
            '<?php echo __mEthoD__;',
        ];

        yield [
            '<?php echo __NAMESPACE__;',
            '<?php echo __namespace__;',
        ];

        yield [
            '<?php echo __TRAIT__;',
            '<?php echo __trait__;',
        ];

        yield [
            '<?php echo __TRAIT__;',
            '<?php echo __trAIt__;',
        ];

        yield [
            '<?php echo Exception::class;',
            '<?php echo Exception::CLASS;',
        ];

        yield [
            '<?php echo Exception::class;',
            '<?php echo Exception::ClAss;',
        ];
    }

    /**
     * @requires PHP <8.0
     *
     * @dataProvider provideFixPre80Cases
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php
                class Bar
                {
                    const __line__ = "foo";
                }

                namespace {
                    echo \Bar::__line__;
                }',
        ];
    }

    /**
     * @requires PHP 8.4
     *
     * @dataProvider provideFix84Cases
     */
    public function testFix84(string $expected, ?string $input = null): void
    {
        $this->testFix($expected, $input);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix84Cases(): iterable
    {
        yield [
            '<?php echo __PROPERTY__;',
            '<?php echo __property__;',
        ];

        yield [
            '<?php echo __PROPERTY__;',
            '<?php echo __PrOpErTy__;',
        ];
    }
}
