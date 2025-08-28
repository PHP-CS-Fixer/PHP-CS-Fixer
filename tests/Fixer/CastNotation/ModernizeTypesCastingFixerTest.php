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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer>
 *
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ModernizeTypesCastingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        $multiLinePatternToFix = <<<'FIX'
            <?php $x =
            intval

            (
                mt_rand
                (
                    0, 100
                )

            )

            ;
            FIX;
        $multiLinePatternFixed = <<<'FIXED'
            <?php $x =
            (int) (
                mt_rand
                (
                    0, 100
                )

            )

            ;
            FIXED;

        $overriddenFunction = <<<'OVERRIDDEN'
            <?php

            class overridesIntval
            {
                public function intval($x)
                {
                    return \intval($x);
                }

                public function usesInval()
                {
                    // that's why it is risky
                    return intval(mt_rand(0, 100));
                }
            }
            OVERRIDDEN;

        $overriddenFunctionFixed = <<<'OVERRIDDEN'
            <?php

            class overridesIntval
            {
                public function intval($x)
                {
                    return (int) $x;
                }

                public function usesInval()
                {
                    // that's why it is risky
                    return (int) (mt_rand(0, 100));
                }
            }
            OVERRIDDEN;

        yield ['<?php $x = "intval";'];

        yield ['<?php $x = ClassA::intval(mt_rand(0, 100));'];

        yield ['<?php $x = ScopeA\intval(mt_rand(0, 100));'];

        yield ['<?php $x = namespace\intval(mt_rand(0, 100));'];

        yield ['<?php $x = $object->intval(mt_rand(0, 100));'];

        yield ['<?php $x = new \intval(mt_rand(0, 100));'];

        yield ['<?php $x = new intval(mt_rand(0, 100));'];

        yield ['<?php $x = new ScopeB\intval(mt_rand(0, 100));'];

        yield ['<?php intvalSmth(mt_rand(0, 100));'];

        yield ['<?php smth_intval(mt_rand(0, 100));'];

        yield ['<?php "SELECT ... intval(mt_rand(0, 100)) ...";'];

        yield ['<?php "test" . "intval" . "in concatenation";'];

        yield ['<?php $x = intval($x, 16);'];

        yield ['<?php $x = intval($x, $options["base"]);'];

        yield ['<?php $x = intval($x, $options->get("base", 16));'];

        yield ['<?php $x = (int) $x;', '<?php $x = intval($x);'];

        yield ['<?php $x = (float) $x;', '<?php $x = floatval($x);'];

        yield ['<?php $x = (float) $x;', '<?php $x = doubleval($x);'];

        yield ['<?php $x = (string) $x;', '<?php $x = strval($x);'];

        yield ['<?php $x = (bool) $x;', '<?php $x = boolval   (  $x  );'];

        yield ['<?php $x = (int) (mt_rand(0, 100));', '<?php $x = intval(mt_rand(0, 100));'];

        yield ['<?php $x = (int) (mt_rand(0, 100));', '<?php $x = \intval(mt_rand(0, 100));'];

        yield ['<?php $x = (int) (mt_rand(0, 100)).".dist";', '<?php $x = intval(mt_rand(0, 100)).".dist";'];

        yield ['<?php $x = (int) (mt_rand(0, 100)).".dist";', '<?php $x = \intval(mt_rand(0, 100)).".dist";'];

        yield [$multiLinePatternFixed, $multiLinePatternToFix];

        yield [$overriddenFunctionFixed, $overriddenFunction];

        yield [
            '<?php $a = (string) ($b . $c);',
            '<?php $a = strval($b . $c);',
        ];

        yield [
            '<?php $x = /**/(int) /**/ /** x*/(/**//** */mt_rand(0, 100)/***/)/*xx*/;',
            '<?php $x = /**/intval/**/ /** x*/(/**//** */mt_rand(0, 100)/***/)/*xx*/;',
        ];

        yield [
            '<?php $x = (string) ((int) ((int) $x + (float) $x));',
            '<?php $x = strval(intval(intval($x) + floatval($x)));',
        ];

        yield [
            '<?php intval();intval(1,2,3);',
        ];

        yield [
            '<?php
                interface Test
                {
                    public function floatval($a);
                    public function &doubleval($a);
                }',
        ];

        yield [
            '<?php $foo = ((int) $x)**2;',
            '<?php $foo = intval($x)**2;',
        ];

        yield [
            '<?php $foo = ((string) $x)[0];',
            '<?php $foo = strval($x)[0];',
        ];

        yield [
            '<?php $foo = ((string) ($x + $y))[0];',
            '<?php $foo = strval($x + $y)[0];',
        ];

        yield [
            '<?php $a = (int) $b;',
            '<?php $a = intval($b, );',
        ];

        yield [
            '<?php $a = (int) $b;',
            '<?php $a = intval($b , );',
        ];

        yield [
            '<?php $a = (string) ($b . $c);',
            '<?php $a = strval($b . $c, );',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php $foo = ((string) ($x + $y)){0};',
            '<?php $foo = strval($x + $y){0};',
        ];

        yield [
            '<?php $a = #
#
#
(int) #
 (
#
 $b#
 )#
 ;#',
            '<?php $a = #
#
\
#
intval#
 (
#
 $b#
 )#
 ;#',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php $x = intval(...);',
        ];
    }
}
