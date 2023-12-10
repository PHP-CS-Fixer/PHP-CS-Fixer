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
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer
 */
final class IsNullFixerTest extends AbstractFixerTestCase
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

        yield ['<?php $x = "is_null";'];

        yield ['<?php $x = ClassA::is_null(json_decode($x));'];

        yield ['<?php $x = ScopeA\\is_null(json_decode($x));'];

        yield ['<?php $x = namespace\\is_null(json_decode($x));'];

        yield ['<?php $x = $object->is_null(json_decode($x));'];

        yield ['<?php $x = new \\is_null(json_decode($x));'];

        yield ['<?php $x = new is_null(json_decode($x));'];

        yield ['<?php $x = new ScopeB\\is_null(json_decode($x));'];

        yield ['<?php is_nullSmth(json_decode($x));'];

        yield ['<?php smth_is_null(json_decode($x));'];

        yield ['<?php namespace Foo; function &is_null($x) { return null === $x; }'];

        yield ['<?php "SELECT ... is_null(json_decode($x)) ...";'];

        yield ['<?php "SELECT ... is_null(json_decode($x)) ...";'];

        yield ['<?php "test" . "is_null" . "in concatenation";'];

        yield ['<?php $x = null === json_decode($x);', '<?php $x = is_null(json_decode($x));'];

        yield ['<?php $x = null !== json_decode($x);', '<?php $x = !is_null(json_decode($x));'];

        yield ['<?php $x = null !== json_decode($x);', '<?php $x = ! is_null(json_decode($x));'];

        yield ['<?php $x = null !== json_decode($x);', '<?php $x = ! is_null( json_decode($x) );'];

        yield ['<?php $x = null === json_decode($x);', '<?php $x = \\is_null(json_decode($x));'];

        yield ['<?php $x = null !== json_decode($x);', '<?php $x = !\\is_null(json_decode($x));'];

        yield ['<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null(json_decode($x));'];

        yield ['<?php $x = null !== json_decode($x);', '<?php $x = ! \\is_null( json_decode($x) );'];

        yield ['<?php $x = null === json_decode($x).".dist";', '<?php $x = is_null(json_decode($x)).".dist";'];

        yield ['<?php $x = null !== json_decode($x).".dist";', '<?php $x = !is_null(json_decode($x)).".dist";'];

        yield ['<?php $x = null === json_decode($x).".dist";', '<?php $x = \\is_null(json_decode($x)).".dist";'];

        yield ['<?php $x = null !== json_decode($x).".dist";', '<?php $x = !\\is_null(json_decode($x)).".dist";'];

        yield [$multiLinePatternFixed, $multiLinePatternToFix];

        yield [
            '<?php $x = /**/null === /**/ /** x*//**//** */json_decode($x)/***//*xx*/;',
            '<?php $x = /**/is_null/**/ /** x*/(/**//** */json_decode($x)/***/)/*xx*/;',
        ];

        yield [
            '<?php $x = null === (null === $x ? z(null === $y) : z(null === $z));',
            '<?php $x = is_null(is_null($x) ? z(is_null($y)) : z(is_null($z)));',
        ];

        yield [
            '<?php $x = null === ($x = array());',
            '<?php $x = is_null($x = array());',
        ];

        yield [
            '<?php null === a(null === a(null === a(null === b())));',
            '<?php \is_null(a(\is_null(a(\is_null(a(\is_null(b())))))));',
        ];

        yield [
            '<?php $d= null === ($a)/*=?*/?>',
            "<?php \$d= is_null(\n(\$a)/*=?*/\n)?>",
        ];

        yield [
            '<?php is_null()?>',
        ];

        // edge cases: is_null wrapped into a binary operations
        yield [
            '<?php $result = (false === (null === $a)); ?>',
            '<?php $result = (false === is_null($a)); ?>',
        ];

        yield [
            '<?php $result = ((null === $a) === false); ?>',
            '<?php $result = (is_null($a) === false); ?>',
        ];

        yield [
            '<?php $result = (false !== (null === $a)); ?>',
            '<?php $result = (false !== is_null($a)); ?>',
        ];

        yield [
            '<?php $result = ((null === $a) !== false); ?>',
            '<?php $result = (is_null($a) !== false); ?>',
        ];

        yield [
            '<?php $result = (false == (null === $a)); ?>',
            '<?php $result = (false == is_null($a)); ?>',
        ];

        yield [
            '<?php $result = ((null === $a) == false); ?>',
            '<?php $result = (is_null($a) == false); ?>',
        ];

        yield [
            '<?php $result = (false != (null === $a)); ?>',
            '<?php $result = (false != is_null($a)); ?>',
        ];

        yield [
            '<?php $result = ((null === $a) != false); ?>',
            '<?php $result = (is_null($a) != false); ?>',
        ];

        yield [
            '<?php $result = (false <> (null === $a)); ?>',
            '<?php $result = (false <> is_null($a)); ?>',
        ];

        yield [
            '<?php $result = ((null === $a) <> false); ?>',
            '<?php $result = (is_null($a) <> false); ?>',
        ];

        yield [
            '<?php if (null === $x) echo "foo"; ?>',
            '<?php if (is_null($x)) echo "foo"; ?>',
        ];

        // check with logical operator
        yield [
            '<?php if (null === $x && $y) echo "foo"; ?>',
            '<?php if (is_null($x) && $y) echo "foo"; ?>',
        ];

        yield [
            '<?php if (null === $x || $y) echo "foo"; ?>',
            '<?php if (is_null($x) || $y) echo "foo"; ?>',
        ];

        yield [
            '<?php if (null === $x xor $y) echo "foo"; ?>',
            '<?php if (is_null($x) xor $y) echo "foo"; ?>',
        ];

        yield [
            '<?php if (null === $x and $y) echo "foo"; ?>',
            '<?php if (is_null($x) and $y) echo "foo"; ?>',
        ];

        yield [
            '<?php if (null === $x or $y) echo "foo"; ?>',
            '<?php if (is_null($x) or $y) echo "foo"; ?>',
        ];

        yield [
            '<?php if ((null === $u or $v) and ($w || null === $x) xor (null !== $y and $z)) echo "foo"; ?>',
            '<?php if ((is_null($u) or $v) and ($w || is_null($x)) xor (!is_null($y) and $z)) echo "foo"; ?>',
        ];

        // edge cases: $isContainingDangerousConstructs, $wrapIntoParentheses
        yield [
            '<?php null === ($a ? $x : $y);',
            '<?php is_null($a ? $x : $y);',
        ];

        yield [
            '<?php $a === (null === $x);',
            '<?php $a === is_null($x);',
        ];

        yield [
            '<?php $a === (null === ($a ? $x : $y));',
            '<?php $a === is_null($a ? $x : $y);',
        ];

        yield [
            '<?php null !== ($a ?? null);',
            '<?php !is_null($a ?? null);',
        ];

        yield [
            '<?php null === $x;',
            '<?php is_null($x, );',
        ];

        yield [
            '<?php null === $x;',
            '<?php is_null( $x , );',
        ];

        yield [
            '<?php null === a(null === a(null === a(null === b(), ), ), );',
            '<?php \is_null(a(\is_null(a(\is_null(a(\is_null(b(), ), ), ), ), ), ), );',
        ];

        yield [
            '<?php if ((null === $u or $v) and ($w || null === $x) xor (null !== $y and $z)) echo "foo"; ?>',
            '<?php if ((is_null($u, ) or $v) and ($w || is_null($x, )) xor (!is_null($y, ) and $z)) echo "foo"; ?>',
        ];

        // edge cases: $isContainingDangerousConstructs, $wrapIntoParentheses
        yield [
            '<?php null === ($a ? $x : $y );',
            '<?php is_null($a ? $x : $y, );',
        ];

        yield [
            '<?php $a === (null === $x);',
            '<?php $a === is_null($x, );',
        ];

        yield [
            '<?php $a === (null === ($a ? $x : $y ));',
            '<?php $a === is_null($a ? $x : $y, );',
        ];

        yield [
            '<?php $a === (int) (null === $x) + (int) (null !== $y);',
            '<?php $a === (int) is_null($x) + (int) !is_null($y);',
        ];
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

    public static function provideFix81Cases(): iterable
    {
        yield 'first-class callable' => [
            '<?php array_filter([], is_null(...));',
        ];

        yield 'first-class callable with leading slash' => [
            '<?php array_filter([], \is_null(...));',
        ];
    }
}
