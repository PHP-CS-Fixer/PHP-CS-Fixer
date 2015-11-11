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

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author Israel Shirk <israelshirk@gmail.com>
 *
 * @internal
 */
final class CommonOperatorWhitespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            // T_???
            array(
                '<?php $a = $b;',
                '<?php $a=$b;'
            ),

            // T_BOOLEAN_AND
            array(
                '<?php if ($a && $b) { }',
                '<?php if ($a&&$b) { }',
            ),

            // T_BOOLEAN_OR
            array(
                '<?php if ($a || $b) { }',
                '<?php if ($a||$b) { }',
            ),

            // T_AND_EQUAL
            array(
                '<?php $a &= $b;',
                '<?php $a&=$b;',
            ),

            // T_CONCAT_EQUAL
            array(
                '<?php $a .= $b;',
                '<?php $a.=$b;',
            ),

            // T_DIV_EQUAL
            array(
                '<?php $a /= $b;',
                '<?php $a/=$b;',
            ),

            // T_IS_EQUAL
            array(
                '<?php $a == $b;',
                '<?php $a==$b;',
            ),

            // T_IS_GREATER_OR_EQUAL
            array(
                '<?php $a >= $b;',
                '<?php $a>=$b;',
            ),

            // T_IS_IDENTICAL
            array(
                '<?php $a === $b;',
                '<?php $a===$b;',
            ),

            // T_IS_NOT_EQUAL
            array(
                '<?php $a != $b;',
                '<?php $a!=$b;',
            ),
            array(
                '<?php $a <> $b;',
                '<?php $a<>$b;',
            ),

            // T_IS_NOT_IDENTICAL
            array(
                '<?php $a !== $b;',
                '<?php $a!==$b;',
            ),

            // T_IS_SMALLER_OR_EQUAL
            array(
                '<?php $a <= $b;',
                '<?php $a<=$b;',
            ),

            // T_MINUS_EQUAL
            array(
                '<?php $a -= $b;',
                '<?php $a-=$b;',
            ),

            // T_MOD_EQUAL
            array(
                '<?php $a %= $b;',
                '<?php $a%=$b;',
            ),

            // T_MUL_EQUAL
            array(
                '<?php $a *= $b;',
                '<?php $a*=$b;',
            ),

            // T_OR_EQUAL
            array(
                '<?php $a |= $b;',
                '<?php $a|=$b;',
            ),

            // T_PLUS_EQUAL
            array(
                '<?php $a += $b;',
                '<?php $a+=$b;',
            ),

            // T_POW_EQUAL
            array(
                '<?php $a **= $b;',
                '<?php $a**=$b;',
            ),

            // T_SL_EQUAL
            array(
                '<?php $a <<= $b;',
                '<?php $a<<=$b;',
            ),

            // T_SR_EQUAL
            array(
                '<?php $a >>= $b;',
                '<?php $a>>=$b;',
            ),

            // T_XOR_EQUAL
            array(
                '<?php $a ^= $b;',
                '<?php $a^=$b;',
            ),

            // T_DOUBLE_ARROW
            array(
                '<?php array($a => $b);',
                '<?php array($a=>$b);',
            ),
        );
    }
}
