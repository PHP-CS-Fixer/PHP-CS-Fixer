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

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author SpacePossum
 *
 * @internal
 */
class FunctionTypehintDefaultFixerTest extends AbstractFixerTestBase
{
    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php function a( array $a1 = array()){}',
                '<?php function a( $a1 = array()){}',
            ),
            array(
                '<?php function b($b, array $a1 = array()){}',
                '<?php function b($b, $a1 = array()){}',
            ),
            array(
                '<?php function c($b, array $a1 = array(),   /**/ $c){}',
                '<?php function c($b, $a1 = array(),   /**/ $c){}',
            ),
            array(
                '<?php function d(array $a1 = array(), $z = null, array $b1 = array(), array $c = array(), $d = 1){}',
                '<?php function d($a1 = array(), $z = null, $b1 = array(), $c = array(), $d = 1){}',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider provide54Cases
     *
     * @requires PHP 5.4
     */
    public function testFix54($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provide54Cases()
    {
        $cases = array(
            array(
                '<?php function a(array $a = []){}',
                '<?php function a($a = []){}',
            ),
            array(
                '<?php function mixed(array $a = [], array $b = array()){}',
                '<?php function mixed($a = [], $b = array()){}',
            ),
        );

        $longArraySyntaxCases = $this->provideCases();
        foreach ($longArraySyntaxCases as $longCase) {
            $cases[] = array(
                str_replace('array()', '[]', $longCase[0]),
                str_replace('array()', '[]', $longCase[1]),
            );
        }

        return $cases;
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider provide56Cases
     *
     * @requires PHP 5.6
     */
    public function testFix56($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provide56Cases()
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
            ),
            array(
                '<?php function foo(array & ...$param) {}',
            ),
        );
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider provide70Cases
     *
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provide70Cases()
    {
        return array(
            array('<?php use function some\test\{fn_a, fn_b, fn_c};'),
            array('<?php use function some\test\{fn_a, fn_b, fn_c} ?>'),
        );
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideDoNotFixCases()
    {
        return array(
            array(
                '<?php function e( ){}',
            ),
            array(
                '<?php function f(){}',
            ),
        );
    }
}
