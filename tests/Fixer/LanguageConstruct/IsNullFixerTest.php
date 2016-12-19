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
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 */
final class IsNullFixerTest extends AbstractFixerTestCase
{
    /**
     * @expectedException PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessage Unknown configuration item "yoda", expected any of "use_yoda_style".
     */
    public function testConfigurationWrongOption()
    {
        $fixer = new IsNullFixer();
        $fixer->configure(array('yoda' => true));
    }

    /**
     * @expectedException PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessage Expected boolean got "integer"
     */
    public function testConfigurationWrongValue()
    {
        $fixer = new IsNullFixer();
        $fixer->configure(array('use_yoda_style' => -1));
    }

    public function testCorrectConfiguration()
    {
        $fixer = new IsNullFixer();
        $fixer->configure(array('use_yoda_style' => false));

        $configuration = static::getObjectAttribute($fixer, 'configuration');
        static::assertFalse($configuration['use_yoda_style']);
    }

    /**
     * @dataProvider provideExamples
     *
     * @param mixed      $expected
     * @param null|mixed $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
    {
        $multiLinePatternToFix = <<<'FIX'
<?php $x =
is_null

(
    json_decode
    (
        $x
    )

)

;
FIX;
        $multiLinePatternFixed = <<<'FIXED'
<?php $x =
null === json_decode
    (
        $x
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
            array(
                '<?php $x = /**/null === /**/ /** x*//**//** */json_decode($x)/***//*xx*/;',
                '<?php $x = /**/is_null/**/ /** x*/(/**//** */json_decode($x)/***/)/*xx*/;',
            ),
            array(
                '<?php $x = null === (null === $x ? z(null === $y) : z(null === $z));',
                '<?php $x = is_null(is_null($x) ? z(is_null($y)) : z(is_null($z)));',
            ),
            array(
                '<?php $x = null === ($x = array());',
                '<?php $x = is_null($x = array());',
            ),
        );
    }
}
