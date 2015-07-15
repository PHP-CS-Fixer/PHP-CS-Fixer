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
    json_decode
    (
        \$x
    )

)

;
FIX;
        $multiLinePatternFixed = <<<FIXED
<?php \$x =
null ===
    json_decode
    (
        \$x
    )

;
FIXED;

        return array(
            array('<?php $x = "is_null";'),

            array('<?php $x = ClassA::is_null(json_decode($x));'),
            array('<?php $x = ScopeA\\is_null(json_decode($x));'),
            array('<?php $x = namespace\\is_null(json_decode($x));'),
            array('<?php $x = $object->is_null(json_decode($x));'),

            array('<?php $x = new \\is_null(json_decode($x));'),
            array('<?php $x = new is_null(json_decode($x));'),
            array('<?php $x = new ScopeB\\is_null(json_decode($x));'),

            array('<?php is_nullSmth(json_decode($x));'),
            array('<?php smth_is_null(json_decode($x));'),

            array('<?php "SELECT ... is_null(json_decode($x)) ...";'),
            array('<?php "SELECT ... is_null(json_decode($x)) ...";'),
            array('<?php "test" . "is_null" . "in concatenation";'),

            array('<?php $x = null === json_decode($x);', '<?php $x = is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = !is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! is_null( json_decode($x) );'),

            array('<?php $x = null === json_decode($x);', '<?php $x = \\is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = !\\is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null(json_decode($x));'),
            array('<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null( json_decode($x) );'),

            array('<?php $x = null === json_decode($x).".dist";', '<?php $x = is_null(json_decode($x)).".dist";'),
            array('<?php $x = null !== json_decode($x).".dist";', '<?php $x = !is_null(json_decode($x)).".dist";'),
            array('<?php $x = null === json_decode($x).".dist";', '<?php $x = \\is_null(json_decode($x)).".dist";'),
            array('<?php $x = null !== json_decode($x).".dist";', '<?php $x = !\\is_null(json_decode($x)).".dist";'),

            array($multiLinePatternFixed, $multiLinePatternToFix),
            array('<?php $x = /**/null === /**//** */json_decode($x)/***//*xx*/;', '<?php $x = /**/is_null/**/ /** x*/(/**//** */json_decode($x)/***/)/*xx*/;'),
        );
    }
}
