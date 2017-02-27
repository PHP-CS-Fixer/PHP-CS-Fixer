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

        return array(
            array('<?php $x = "dirname";'),

            array('<?php $x = dirname(__FILE__.".dist");'),

            array('<?php $x = ClassA::dirname(__FILE__);'),
            array('<?php $x = ScopeA\\dirname(__FILE__);'),
            array('<?php $x = namespace\\dirname(__FILE__);'),
            array('<?php $x = $object->dirname(__FILE__);'),

            array('<?php $x = new \\dirname(__FILE__);'),
            array('<?php $x = new dirname(__FILE__);'),
            array('<?php $x = new ScopeB\\dirname(__FILE__);'),

            array('<?php dirnameSmth(__FILE__);'),
            array('<?php smth_dirname(__FILE__);'),

            array('<?php "SELECT ... dirname(__FILE__) ...";'),
            array('<?php "SELECT ... DIRNAME(__FILE__) ...";'),
            array('<?php "test" . "dirname" . "in concatenation";'),

            array('<?php $x = dirname(__DIR__);', '<?php $x = dirname(dirname(__FILE__));'),
            array('<?php $x = __DIR__;', '<?php $x = dirname(__FILE__);'),
            array('<?php $x = __DIR__;', '<?php $x = \\dirname(__FILE__);'),
            array('<?php $x = __DIR__.".dist";', '<?php $x = dirname(__FILE__).".dist";'),
            array('<?php $x = __DIR__.".dist";', '<?php $x = \\dirname(__FILE__).".dist";'),

            array($multiLinePatternFixed, $multiLinePatternToFix),
            array(
                '<?php $x = /**//**/ /** x*//**//** */__DIR__/***//*xx*/;',
                '<?php $x = /**/dirname/**/ /** x*/(/**//** */__FILE__/***/)/*xx*/;',
            ),
            array(
                '<?php
                interface Test
                {
                    public function dirname($a);
                }',
            ),
            array(
                '<?php
                interface Test
                {
                    public function &dirname($a);
                }',
            ),
            array(
                "<?php echo __DIR__\n?>",
                "<?php echo dirname\n(\n__FILE__\n)\n?>",
            ),
        );
    }
}
