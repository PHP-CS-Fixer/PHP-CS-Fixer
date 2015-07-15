<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
class DirConstantFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        try {
            $this->makeTest($expected, $input);
        } catch (\RuntimeException $e) {
            $this->assertTrue(false, $expected.' -> '.$e->getMessage());
        }
    }

    public function provideExamples()
    {
        $multiLinePatternToFix = <<<FIX
<?php \$x =
dirname

(

    __FILE__

)

;
FIX;
        $multiLinePatternFixed = <<<FIXED
<?php \$x =
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

            array('<?php $x = __DIR__;', '<?php $x = dirname(__FILE__);'),
            array('<?php $x = __DIR__;', '<?php $x = \\dirname(__FILE__);'),
            array('<?php $x = __DIR__.".dist";', '<?php $x = dirname(__FILE__).".dist";'),
            array('<?php $x = __DIR__.".dist";', '<?php $x = \\dirname(__FILE__).".dist";'),

            array($multiLinePatternFixed, $multiLinePatternToFix),
        );
    }
}
