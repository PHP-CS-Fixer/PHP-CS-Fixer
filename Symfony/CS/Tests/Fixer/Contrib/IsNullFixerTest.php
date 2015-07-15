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
class IsNullFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideExamples()
    {
        $multiLinePatternToFix = <<<FIX
<?php \$x =
is_null

(
    file_get_contents
    (
        \$x
    )

)

;
FIX;
        $multiLinePatternFixed = <<<FIXED
<?php \$x =
null ===
    file_get_contents
    (
        \$x
    )

;
FIXED;

        return array(
            array('<?php $x = "is_null";'),

            array('<?php $x = ClassA::is_null(file_get_contents($x));'),
            array('<?php $x = ScopeA\\is_null(file_get_contents($x));'),
            array('<?php $x = namespace\\is_null(file_get_contents($x));'),
            array('<?php $x = $object->is_null(file_get_contents($x));'),

            array('<?php $x = new \\is_null(file_get_contents($x));'),
            array('<?php $x = new is_null(file_get_contents($x));'),
            array('<?php $x = new ScopeB\\is_null(file_get_contents($x));'),

            array('<?php is_nullSmth(file_get_contents($x));'),
            array('<?php smth_is_null(file_get_contents($x));'),

            array('<?php "SELECT ... is_null(file_get_contents($x)) ...";'),
            array('<?php "SELECT ... is_null(file_get_contents($x)) ...";'),
            array('<?php "test" . "is_null" . "in concatenation";'),

            array('<?php $x = null === file_get_contents($x);', '<?php $x = is_null(file_get_contents($x));'),
            array('<?php $x = null !== file_get_contents($x);', '<?php $x = !is_null(file_get_contents($x));'),
            array('<?php $x = null !== file_get_contents($x);', '<?php $x = ! is_null(file_get_contents($x));'),
            array('<?php $x = null !== file_get_contents($x);', '<?php $x = ! is_null( file_get_contents($x) );'),

            array('<?php $x = null === file_get_contents($x);', '<?php $x = \\is_null(file_get_contents($x));'),
            array('<?php $x = null !== file_get_contents($x);', '<?php $x = !\\is_null(file_get_contents($x));'),
            array('<?php $x = null !== file_get_contents($x);', '<?php $x = ! \\is_null(file_get_contents($x));'),
            array('<?php $x = null !== file_get_contents($x);', '<?php $x = ! \\is_null( file_get_contents($x) );'),

            array('<?php $x = null === file_get_contents($x).".dist";', '<?php $x = is_null(file_get_contents($x)).".dist";'),
            array('<?php $x = null !== file_get_contents($x).".dist";', '<?php $x = !is_null(file_get_contents($x)).".dist";'),
            array('<?php $x = null === file_get_contents($x).".dist";', '<?php $x = \\is_null(file_get_contents($x)).".dist";'),
            array('<?php $x = null !== file_get_contents($x).".dist";', '<?php $x = !\\is_null(file_get_contents($x)).".dist";'),

            array($multiLinePatternFixed, $multiLinePatternToFix),
            array('<?php $x = /**/null === /**//** */file_get_contents($x)/***//*xx*/;', '<?php $x = /**/is_null/**/ /** x*/(/**//** */file_get_contents($x)/***/)/*xx*/;'),
        );
    }
}
