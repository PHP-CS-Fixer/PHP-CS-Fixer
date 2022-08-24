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
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer
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

    public function provideFixCases(): iterable
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

        yield from [
            ['<?php $x = "intval";'],

            ['<?php $x = ClassA::intval(mt_rand(0, 100));'],
            ['<?php $x = ScopeA\\intval(mt_rand(0, 100));'],
            ['<?php $x = namespace\\intval(mt_rand(0, 100));'],
            ['<?php $x = $object->intval(mt_rand(0, 100));'],

            ['<?php $x = new \\intval(mt_rand(0, 100));'],
            ['<?php $x = new intval(mt_rand(0, 100));'],
            ['<?php $x = new ScopeB\\intval(mt_rand(0, 100));'],

            ['<?php intvalSmth(mt_rand(0, 100));'],
            ['<?php smth_intval(mt_rand(0, 100));'],

            ['<?php "SELECT ... intval(mt_rand(0, 100)) ...";'],
            ['<?php "test" . "intval" . "in concatenation";'],

            ['<?php $x = intval($x, 16);'],
            ['<?php $x = intval($x, $options["base"]);'],
            ['<?php $x = intval($x, $options->get("base", 16));'],

            ['<?php $x = (int) $x;', '<?php $x = intval($x);'],
            ['<?php $x = (float) $x;', '<?php $x = floatval($x);'],
            ['<?php $x = (float) $x;', '<?php $x = doubleval($x);'],
            ['<?php $x = (string) $x;', '<?php $x = strval($x);'],
            ['<?php $x = (bool) $x;', '<?php $x = boolval   (  $x  );'],
            ['<?php $x = (int) (mt_rand(0, 100));', '<?php $x = intval(mt_rand(0, 100));'],
            ['<?php $x = (int) (mt_rand(0, 100));', '<?php $x = \\intval(mt_rand(0, 100));'],
            ['<?php $x = (int) (mt_rand(0, 100)).".dist";', '<?php $x = intval(mt_rand(0, 100)).".dist";'],
            ['<?php $x = (int) (mt_rand(0, 100)).".dist";', '<?php $x = \\intval(mt_rand(0, 100)).".dist";'],

            [$multiLinePatternFixed, $multiLinePatternToFix],
            [$overriddenFunctionFixed, $overriddenFunction],

            [
                '<?php $a = (string) ($b . $c);',
                '<?php $a = strval($b . $c);',
            ],
            [
                '<?php $x = /**/(int) /**/ /** x*/(/**//** */mt_rand(0, 100)/***/)/*xx*/;',
                '<?php $x = /**/intval/**/ /** x*/(/**//** */mt_rand(0, 100)/***/)/*xx*/;',
            ],
            [
                '<?php $x = (string) ((int) ((int) $x + (float) $x));',
                '<?php $x = strval(intval(intval($x) + floatval($x)));',
            ],
            [
                '<?php intval();intval(1,2,3);',
            ],
            [
                '<?php
                interface Test
                {
                    public function floatval($a);
                    public function &doubleval($a);
                }',
            ],
            [
                '<?php $foo = ((int) $x)**2;',
                '<?php $foo = intval($x)**2;',
            ],
            [
                '<?php $foo = ((string) $x)[0];',
                '<?php $foo = strval($x)[0];',
            ],
            [
                '<?php $foo = ((string) ($x + $y))[0];',
                '<?php $foo = strval($x + $y)[0];',
            ],
            [
                '<?php $a = (int) $b;',
                '<?php $a = intval($b, );',
            ],
            [
                '<?php $a = (int) $b;',
                '<?php $a = intval($b , );',
            ],
            [
                '<?php $a = (string) ($b . $c);',
                '<?php $a = strval($b . $c, );',
            ],
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php $foo = ((string) ($x + $y)){0};',
            '<?php $foo = strval($x + $y){0};',
        ];
    }

    /**
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(): void
    {
        $this->doTest(
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
 ;#'
        );
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

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php $x = intval(...);',
        ];
    }
}
