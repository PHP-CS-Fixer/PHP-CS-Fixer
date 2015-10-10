<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FunctionTypehintSpaceFixerTest extends AbstractFixerTestCase
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
            array(
                '<?php function foo($param) {}',
            ),
            array(
                '<?php function foo($param1,$param2) {}',
            ),
            array(
                '<?php function foo(&$param) {}',
            ),
            array(
                '<?php function foo(& $param) {}',
            ),
            array(
                '<?php function foo(/**int*/$param) {}',
            ),
            array(
                '<?php function foo(callable $param) {}',
                '<?php function foo(callable$param) {}',
            ),
            array(
                '<?php function foo(array &$param) {}',
                '<?php function foo(array&$param) {}',
            ),
            array(
                '<?php function foo(array & $param) {}',
                '<?php function foo(array& $param) {}',
            ),
            array(
                '<?php function foo(Bar $param) {}',
                '<?php function foo(Bar$param) {}',
            ),
            array(
                '<?php function foo(Bar\Baz $param) {}',
                '<?php function foo(Bar\Baz$param) {}',
            ),
            array(
                '<?php function foo(Bar\Baz &$param) {}',
                '<?php function foo(Bar\Baz&$param) {}',
            ),
            array(
                '<?php function foo(Bar\Baz & $param) {}',
                '<?php function foo(Bar\Baz& $param) {}',
            ),
            array(
                '<?php $foo = function(Bar\Baz $param) {}',
                '<?php $foo = function(Bar\Baz$param) {}',
            ),
            array(
                '<?php $foo = function(Bar\Baz &$param) {}',
                '<?php $foo = function(Bar\Baz&$param) {}',
            ),
            array(
                '<?php $foo = function(Bar\Baz & $param) {}',
                '<?php $foo = function(Bar\Baz& $param) {}',
            ),
            array(
                '<?php class Test { public function foo(Bar\Baz $param) {} }',
                '<?php class Test { public function foo(Bar\Baz$param) {} }',
            ),
            array(
                '<?php $foo = function(array $a,
                    array $b, array     $c, array
                    $d) {};',
                '<?php $foo = function(array $a,
                    array$b, array     $c, array
                    $d) {};',
            ),
        );
    }

    /**
     * @dataProvider provideCases56
     * @requires PHP 5.6
     */
    public function testFix56($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases56()
    {
        return array(
            array(
                '<?php function foo(...$param) {}',
            ),
            array(
                '<?php function foo(&...$param) {}',
            ),
            array(
                '<?php function foo(array ...$param) {}',
                '<?php function foo(array...$param) {}',
            ),
            array(
                '<?php function foo(array & ...$param) {}',
                '<?php function foo(array& ...$param) {}',
            ),
        );
    }
}
