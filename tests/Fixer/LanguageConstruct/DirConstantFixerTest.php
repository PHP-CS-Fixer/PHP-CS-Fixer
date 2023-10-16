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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer
 */
final class DirConstantFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        $multiLinePatternToFix = <<<'FIX'
            <?php $x =
            dirname

            (

                __FILE__

            )

            ;
            FIX;
        $multiLinePatternFixed = <<<'FIXED'
            <?php $x =
            __DIR__

            ;
            FIXED;

        yield ['<?php $x = "dirname";'];

        yield ['<?php $x = dirname(__FILE__.".dist");'];

        yield ['<?php $x = ClassA::dirname(__FILE__);'];

        yield ['<?php $x = ScopeA\\dirname(__FILE__);'];

        yield ['<?php $x = namespace\\dirname(__FILE__);'];

        yield ['<?php $x = $object->dirname(__FILE__);'];

        yield ['<?php $x = new \\dirname(__FILE__);'];

        yield ['<?php $x = new dirname(__FILE__);'];

        yield ['<?php $x = new ScopeB\\dirname(__FILE__);'];

        yield ['<?php dirnameSmth(__FILE__);'];

        yield ['<?php smth_dirname(__FILE__);'];

        yield ['<?php "SELECT ... dirname(__FILE__) ...";'];

        yield ['<?php "SELECT ... DIRNAME(__FILE__) ...";'];

        yield ['<?php "test" . "dirname" . "in concatenation";'];

        yield [
            '<?php $x = dirname(__DIR__);',
            '<?php $x = dirname(dirname(__FILE__));',
        ];

        yield [
            '<?php $x = __DIR__;',
            '<?php $x = dirname(__FILE__);',
        ];

        yield [
            '<?php $x =   /* A */ __DIR__     /* B */;',
            '<?php $x = dirname  (  /* A */ __FILE__  )   /* B */;',
        ];

        yield [
            '<?php $x = __DIR__;',
            '<?php $x = \dirname(__FILE__);',
        ];

        yield [
            '<?php $x = __DIR__.".dist";',
            '<?php $x = dirname(__FILE__).".dist";',
        ];

        yield [
            '<?php $x = __DIR__.".dist";',
            '<?php $x = \dirname(__FILE__).".dist";',
        ];

        yield [
            '<?php $x = /* 0 *//* 1 */ /** x2*//*3*//** 4*/__DIR__/**5*//*xx*/;',
            '<?php $x = /* 0 */dirname/* 1 */ /** x2*/(/*3*//** 4*/__FILE__/**5*/)/*xx*/;',
        ];

        yield [
            '<?php
                interface Test
                {
                    public function dirname($a);
                }',
        ];

        yield [
            '<?php
                interface Test
                {
                    public function &dirname($a);
                }',
        ];

        yield [
            "<?php echo __DIR__\n?>",
            "<?php echo dirname\n(\n__FILE__\n)\n?>",
        ];

        yield [
            "<?php echo __DIR__/*1*/\n?>",
            "<?php echo dirname\n(\n__FILE__/*1*/\n)\n?>",
        ];

        yield [
            $multiLinePatternFixed,
            $multiLinePatternToFix,
        ];

        yield [
            '<?php $x = __DIR__;',
            '<?php $x = \dirname(
                    __FILE__                     '.'
                );',
        ];

        yield [
            '<?php
                    $x = dirname(dirname("a".__FILE__));
                    $x = dirname(dirname(__FILE__."a"));
                    $x = dirname(dirname("a".__FILE__."a"));
                ',
        ];

        yield [
            '<?php $x = __DIR__.".dist";',
            '<?php $x = dirname(__FILE__,   ).".dist";',
        ];

        yield [
            '<?php $x = __DIR__/* a */  /* b */  .".dist";',
            '<?php $x = \dirname(__FILE__/* a */,  /* b */)  .".dist";',
        ];

        yield [
            '<?php $x = __DIR__;',
            '<?php $x = \dirname(
                    __FILE__   ,                     '.'
                );',
        ];
    }

    /**
     * @requires PHP <8.0
     */
    public function testFixPrePHP80(): void
    {
        $this->doTest(
            '<?php $x =# A
# A1
# B
# C
__DIR__# D
# E
;# F
',
            '<?php $x =# A
\
# A1
dirname# B
(# C
__FILE__# D
)# E
;# F
'
        );
    }
}
