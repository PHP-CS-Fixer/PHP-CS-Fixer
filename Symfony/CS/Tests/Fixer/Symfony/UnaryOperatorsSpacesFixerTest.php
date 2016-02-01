<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
class UnaryOperatorsSpacesFixerTest extends AbstractFixerTestBase
{
    /**
     * {@inheritdoc}
     */
    protected function isLintException($source)
    {
        return in_array($source, array(
            '<?php foo(+ $a, - 2,- $b, & $c);',
            '<?php foo(+$a, -2,-$b, &$c);',
        ), true);
    }

    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        $cases = array(
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
                '<?php function foo(&$a, array &$b, Bar &$c) {}',
                '<?php function foo(& $a, array & $b, Bar & $c) {}',
            ),
            array(
                '<?php foo(+$a, -2,-$b, &$c);',
                '<?php foo(+ $a, - 2,- $b, & $c);',
            ),
        );

        return $cases;
    }

    /**
     * @dataProvider provideCases56
     * @requires PHP 5.6
     */
    public function testFix56($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases56()
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
