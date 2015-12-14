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
 * @author SpacePossum
 *
 * @internal
 */
final class UselessReturnFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
    function b($b) {
        if ($b) {
            return;
        }
        /**/
    }',
                '<?php
    function b($b) {
        if ($b) {
            return;
        }
        return /**/;
    }',
            ),
            array(
                '<?php
    class Test2
    {
        private static function a($a)
        {
            if ($a) {
                return;
            }

            $c1 = function() use ($a) {
                if ($a)
                    return;
                if ($a > 1) return;
                echo $a;
                '.'
            };
            $c1();
            '.'
        }
    }',
                '<?php
    class Test2
    {
        private static function a($a)
        {
            if ($a) {
                return;
            }

            $c1 = function() use ($a) {
                if ($a)
                    return;
                if ($a > 1) return;
                echo $a;
                return;
            };
            $c1();
            return
            ;
        }
    }',
            ),
            array(
                '<?php
    function aT($a) {
        if ($a) {
            return;
        }
        '.'
    }',
                '<?php
    function aT($a) {
        if ($a) {
            return;
        }
        return           ;
    }',
            ),
            array(
                '<?php return;',
            ),
            array(
                '<?php
    function c($c) {
        if ($c) {
            return;
        }
        //'.'
    }',
                '<?php
    function c($c) {
        if ($c) {
            return;
        }
        return;//
    }',
            ),
            array(
                '<?php
    class Test {

        private static function d($d) {
            if ($d) {
                return;
            }
            }
    }',
                '<?php
    class Test {

        private static function d($d) {
            if ($d) {
                return;
            }
            return;}
    }',
            ),
        );
    }
}
