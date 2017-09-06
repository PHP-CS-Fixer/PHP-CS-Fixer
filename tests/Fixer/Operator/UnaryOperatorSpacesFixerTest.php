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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer
 */
final class UnaryOperatorSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $cases = array(
            array(
                '<?php $a= 1;$a#
++#
;#',
            ),
            array(
                '<?php $a++;',
                '<?php $a ++;',
            ),
            array(
                '<?php $a--;',
                '<?php $a --;',
            ),
            array(
                '<?php ++$a;',
                '<?php ++ $a;',
            ),
            array(
                '<?php --$a;',
                '<?php -- $a;',
            ),
            array(
                '<?php $a = !$b;',
                '<?php $a = ! $b;',
            ),
            array(
                '<?php $a = !!$b;',
                '<?php $a = ! ! $b;',
            ),
            array(
                '<?php $a = ~$b;',
                '<?php $a = ~ $b;',
            ),
            array(
                '<?php $a = &$b;',
                '<?php $a = & $b;',
            ),
            array(
                '<?php $a=&$b;',
            ),
            array(
                '<?php $a * -$b;',
                '<?php $a * - $b;',
            ),
            array(
                '<?php $a *-$b;',
                '<?php $a *- $b;',
            ),
            array(
                '<?php $a*-$b;',
            ),
            array(
                '<?php function &foo(){}',
                '<?php function & foo(){}',
            ),
            array(
                '<?php function &foo(){}',
                '<?php function &   foo(){}',
            ),
            array(
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
                '<?php function foo(& $a, array & $b, Bar & $c) {}',
            ),
        );

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixLT54Cases
     * @requires PHP <5.4
     */
    public function testFixLT54($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixLT54Cases()
    {
        return array(
            array(
                '<?php function foo() {} foo(+$a, -2,-$b, &$c);',
                '<?php function foo() {} foo(+ $a, - 2,- $b, & $c);',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix56Cases
     * @requires PHP 5.6
     */
    public function testFix56($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix56Cases()
    {
        return array(
            array(
                '<?php function foo($a, ...$b) {}',
                '<?php function foo($a, ... $b) {}',
            ),
            array(
                '<?php function foo(&...$a) {}',
                '<?php function foo(& ... $a) {}',
            ),
            array(
                '<?php function foo(array ...$a) {}',
            ),
            array(
                '<?php foo(...$a);',
                '<?php foo(... $a);',
            ),
            array(
                '<?php foo($a, ...$b);',
                '<?php foo($a, ... $b);',
            ),
        );
    }
}
