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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
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

        return [
            ['<?php $x = "dirname";'],

            ['<?php $x = dirname(__FILE__.".dist");'],

            ['<?php $x = ClassA::dirname(__FILE__);'],
            ['<?php $x = ScopeA\\dirname(__FILE__);'],
            ['<?php $x = namespace\\dirname(__FILE__);'],
            ['<?php $x = $object->dirname(__FILE__);'],

            ['<?php $x = new \\dirname(__FILE__);'],
            ['<?php $x = new dirname(__FILE__);'],
            ['<?php $x = new ScopeB\\dirname(__FILE__);'],

            ['<?php dirnameSmth(__FILE__);'],
            ['<?php smth_dirname(__FILE__);'],

            ['<?php "SELECT ... dirname(__FILE__) ...";'],
            ['<?php "SELECT ... DIRNAME(__FILE__) ...";'],
            ['<?php "test" . "dirname" . "in concatenation";'],

            [
                '<?php $x = dirname(__DIR__);',
                '<?php $x = dirname(dirname(__FILE__));',
            ],
            [
                '<?php $x = __DIR__;',
                '<?php $x = dirname(__FILE__);',
            ],
            [
                '<?php $x = __DIR__;',
                '<?php $x = \\dirname(__FILE__);',
            ],
            [
                '<?php $x = __DIR__.".dist";',
                '<?php $x = dirname(__FILE__).".dist";',
            ],
            [
                '<?php $x = __DIR__.".dist";',
                '<?php $x = \\dirname(__FILE__).".dist";',
            ],
            [$multiLinePatternFixed, $multiLinePatternToFix],
            [
                '<?php $x = /**//**/ /** x*//**//** */__DIR__/***//*xx*/;',
                '<?php $x = /**/dirname/**/ /** x*/(/**//** */__FILE__/***/)/*xx*/;',
            ],
            [
                '<?php
                interface Test
                {
                    public function dirname($a);
                }',
            ],
            [
                '<?php
                interface Test
                {
                    public function &dirname($a);
                }',
            ],
            [
                "<?php echo __DIR__\n?>",
                "<?php echo dirname\n(\n__FILE__\n)\n?>",
            ],
            [
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
',
            ],
        ];
    }
}
