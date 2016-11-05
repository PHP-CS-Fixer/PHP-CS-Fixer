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

namespace PhpCsFixer\Tests\Fixer\CastNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 */
final class ModernizeTypesCastingFixerTest extends AbstractFixerTestCase
{
    /**
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
        // that's why it risky
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
        // that's why it risky
        return (int) (mt_rand(0, 100));
    }
}
OVERRIDDEN;

        return array(
            array('<?php $x = "intval";'),

            array('<?php $x = ClassA::intval(mt_rand(0, 100));'),
            array('<?php $x = ScopeA\\intval(mt_rand(0, 100));'),
            array('<?php $x = namespace\\intval(mt_rand(0, 100));'),
            array('<?php $x = $object->intval(mt_rand(0, 100));'),

            array('<?php $x = new \\intval(mt_rand(0, 100));'),
            array('<?php $x = new intval(mt_rand(0, 100));'),
            array('<?php $x = new ScopeB\\intval(mt_rand(0, 100));'),

            array('<?php intvalSmth(mt_rand(0, 100));'),
            array('<?php smth_intval(mt_rand(0, 100));'),

            array('<?php "SELECT ... intval(mt_rand(0, 100)) ...";'),
            array('<?php "test" . "intval" . "in concatenation";'),

            array('<?php $x = intval($x, 16);'),
            array('<?php $x = intval($x, $options["base"]);'),
            array('<?php $x = intval($x, $options->get("base", 16));'),

            array('<?php $x = (int) $x;', '<?php $x = intval($x);'),
            array('<?php $x = (float) $x;', '<?php $x = floatval($x);'),
            array('<?php $x = (float) $x;', '<?php $x = doubleval($x);'),
            array('<?php $x = (string) $x;', '<?php $x = strval($x);'),
            array('<?php $x = (bool) $x;', '<?php $x = boolval   (  $x  );'),
            array('<?php $x = (int) (mt_rand(0, 100));', '<?php $x = intval(mt_rand(0, 100));'),
            array('<?php $x = (int) (mt_rand(0, 100));', '<?php $x = \\intval(mt_rand(0, 100));'),
            array('<?php $x = (int) (mt_rand(0, 100)).".dist";', '<?php $x = intval(mt_rand(0, 100)).".dist";'),
            array('<?php $x = (int) (mt_rand(0, 100)).".dist";', '<?php $x = \\intval(mt_rand(0, 100)).".dist";'),

            array($multiLinePatternFixed, $multiLinePatternToFix),
            array($overriddenFunctionFixed, $overriddenFunction),

            array(
                '<?php $x = /**/(int) /**/ /** x*/(/**//** */mt_rand(0, 100)/***/)/*xx*/;',
                '<?php $x = /**/intval/**/ /** x*/(/**//** */mt_rand(0, 100)/***/)/*xx*/;',
            ),
            array(
                '<?php $x = (string) ((int) ((int) $x + (float) $x));',
                '<?php $x = strval(intval(intval($x) + floatval($x)));',
            ),
            array(
                '<?php intval();intval(1,2,3);',
            ),
        );
    }
}
