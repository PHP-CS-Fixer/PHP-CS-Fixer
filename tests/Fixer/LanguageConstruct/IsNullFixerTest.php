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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer
 */
final class IsNullFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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

        return [
            ['<?php $x = "is_null";'],

            ['<?php $x = ClassA::is_null(json_decode($x));'],
            ['<?php $x = ScopeA\\is_null(json_decode($x));'],
            ['<?php $x = namespace\\is_null(json_decode($x));'],
            ['<?php $x = $object->is_null(json_decode($x));'],

            ['<?php $x = new \\is_null(json_decode($x));'],
            ['<?php $x = new is_null(json_decode($x));'],
            ['<?php $x = new ScopeB\\is_null(json_decode($x));'],

            ['<?php is_nullSmth(json_decode($x));'],
            ['<?php smth_is_null(json_decode($x));'],

            ['<?php "SELECT ... is_null(json_decode($x)) ...";'],
            ['<?php "SELECT ... is_null(json_decode($x)) ...";'],
            ['<?php "test" . "is_null" . "in concatenation";'],

            ['<?php $x = null === json_decode($x);', '<?php $x = is_null(json_decode($x));'],
            ['<?php $x = null !== json_decode($x);', '<?php $x = !is_null(json_decode($x));'],
            ['<?php $x = null !== json_decode($x);', '<?php $x = ! is_null(json_decode($x));'],
            ['<?php $x = null !== json_decode($x);', '<?php $x = ! is_null( json_decode($x) );'],

            ['<?php $x = null === json_decode($x);', '<?php $x = \\is_null(json_decode($x));'],
            ['<?php $x = null !== json_decode($x);', '<?php $x = !\\is_null(json_decode($x));'],
            ['<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null(json_decode($x));'],
            ['<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null( json_decode($x) );'],

            ['<?php $x = null === json_decode($x).".dist";', '<?php $x = is_null(json_decode($x)).".dist";'],
            ['<?php $x = null !== json_decode($x).".dist";', '<?php $x = !is_null(json_decode($x)).".dist";'],
            ['<?php $x = null === json_decode($x).".dist";', '<?php $x = \\is_null(json_decode($x)).".dist";'],
            ['<?php $x = null !== json_decode($x).".dist";', '<?php $x = !\\is_null(json_decode($x)).".dist";'],

            [$multiLinePatternFixed, $multiLinePatternToFix],
            [
                '<?php $x = /**/null === /**/ /** x*//**//** */json_decode($x)/***//*xx*/;',
                '<?php $x = /**/is_null/**/ /** x*/(/**//** */json_decode($x)/***/)/*xx*/;',
            ],
            [
                '<?php $x = null === (null === $x ? z(null === $y) : z(null === $z));',
                '<?php $x = is_null(is_null($x) ? z(is_null($y)) : z(is_null($z)));',
            ],
            [
                '<?php $x = null === ($x = array());',
                '<?php $x = is_null($x = array());',
            ],
            [
                '<?php null === a(null === a(null === a(null === b())));',
                '<?php \is_null(a(\is_null(a(\is_null(a(\is_null(b())))))));',
            ],
            [
                '<?php $d= null === ($a)/*=?*/?>',
                "<?php \$d= is_null(\n(\$a)/*=?*/\n)?>",
            ],
            [
                '<?php is_null()?>',
            ],

            // edge cases: is_null wrapped into a binary operations
            [
                '<?php $result = (false === (null === $a)); ?>',
                '<?php $result = (false === is_null($a)); ?>',
            ],
            [
                '<?php $result = ((null === $a) === false); ?>',
                '<?php $result = (is_null($a) === false); ?>',
            ],
            [
                '<?php $result = (false !== (null === $a)); ?>',
                '<?php $result = (false !== is_null($a)); ?>',
            ],
            [
                '<?php $result = ((null === $a) !== false); ?>',
                '<?php $result = (is_null($a) !== false); ?>',
            ],
            [
                '<?php $result = (false == (null === $a)); ?>',
                '<?php $result = (false == is_null($a)); ?>',
            ],
            [
                '<?php $result = ((null === $a) == false); ?>',
                '<?php $result = (is_null($a) == false); ?>',
            ],
            [
                '<?php $result = (false != (null === $a)); ?>',
                '<?php $result = (false != is_null($a)); ?>',
            ],
            [
                '<?php $result = ((null === $a) != false); ?>',
                '<?php $result = (is_null($a) != false); ?>',
            ],
            [
                '<?php $result = (false <> (null === $a)); ?>',
                '<?php $result = (false <> is_null($a)); ?>',
            ],
            [
                '<?php $result = ((null === $a) <> false); ?>',
                '<?php $result = (is_null($a) <> false); ?>',
            ],
            [
                '<?php if (null === $x) echo "foo"; ?>',
                '<?php if (is_null($x)) echo "foo"; ?>',
            ],
            // check with logical operator
            [
                '<?php if (null === $x && $y) echo "foo"; ?>',
                '<?php if (is_null($x) && $y) echo "foo"; ?>',
            ],
            [
                '<?php if (null === $x || $y) echo "foo"; ?>',
                '<?php if (is_null($x) || $y) echo "foo"; ?>',
            ],
            [
                '<?php if (null === $x xor $y) echo "foo"; ?>',
                '<?php if (is_null($x) xor $y) echo "foo"; ?>',
            ],
            [
                '<?php if (null === $x and $y) echo "foo"; ?>',
                '<?php if (is_null($x) and $y) echo "foo"; ?>',
            ],
            [
                '<?php if (null === $x or $y) echo "foo"; ?>',
                '<?php if (is_null($x) or $y) echo "foo"; ?>',
            ],
            [
                '<?php if ((null === $u or $v) and ($w || null === $x) xor (null !== $y and $z)) echo "foo"; ?>',
                '<?php if ((is_null($u) or $v) and ($w || is_null($x)) xor (!is_null($y) and $z)) echo "foo"; ?>',
            ],
        ];
    }
}
