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

namespace PhpCsFixer\Tests\Fixer\ReturnNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class ReturnAssignmentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                    function a() {
                          return 1;
                         '.'
                    }
                ',
                '<?php
                    function a() {
                        $a = 1;
                        return $a;
                    }
                ',
            ),
            array(
                '<?php function a($b,$c)  {if($c>1){echo 1;}  return (1 + 2 + $b); }',
                '<?php function a($b,$c)  {if($c>1){echo 1;} $a= (1 + 2 + $b);return $a;}',
            ),
            array(
                '<?php function a($b,$c)  {return (1 + 2 + $b); }',
                '<?php function a($b,$c)  {$a= (1 + 2 + $b);return $a;}',
            ),
            array(
                '<?php
                    function a($zz)
                    {
                        $zz = 1 ?><?php
                        ;
                        return $zz;
                    }
                ',
            ),
            array(
                '<?php
                    function a {
                          return 123;
                         '.'
                    }
                ',
                '<?php
                    function a {
                        $a = 123;
                        return $a;
                    }
                ',
            ),
            array(
                '<?php
                    function a {
                          return 123;
                          ?> <?php
                    }
                ',
                '<?php
                    function a {
                        $a = 123;
                        return $a ?> <?php
                    }
                ',
            ),
            array(
                '<?php
                    function a()
                    {
                          return $c + 1; // var names are case insensitive
                            '.'
                    }',
                '<?php
                    function a()
                    {
                        $A = $c + 1; // var names are case insensitive
                        return $a   ;
                    }',
            ),
            array(
                '<?php
                    function b() {
                        if ($c) {
                              return 0;
                             '.'
                        }
                          return testFunction(123+1);
                         '.'
                    }',
                '<?php
                    function b() {
                        if ($c) {
                            $b = 0;
                            return $b;
                        }
                        $a = testFunction(123+1);
                        return $a;
                    }',
            ),
            // no fix cases
            array(
                '<?php
                    function a() {
                        static $a;
                        $a = time();
                        return $a;
                    }
                ',
            ),
            array(
                '<?php
                    function a() {
                        global $a;
                        $a = time();
                        return $a;
                    }
                ',
            ),
            array(
                '<?php
                function foo(&$var)
                    {
                        $var = 1;
                        return $var;
                    }
                ',
            ),
            array(
                '<?php
                    $a = 1; // var might be global here
                    return $a;
                ',
            ),
            array(
                '<?php
                    function a()
                    {
                        $a = 1;
                        ?>
                        <?php
                        ;
                        return $a;
                    }
                ',
            ),
            array(
                '<?php
                    function a()
                    {
                        $a = 1
                        ?>
                        <?php
                        return $a;
                    }
                ',
            ),
            array(
                '<?php
                    $zz = 1 ?><?php
                    function a($zz)
                    {
                        ;
                        return $zz;
                    }
                ',
            ),
            array(
                '<?php
                    function a($c)
                    {
                        $a = 1;
                        return $a + $c;
                    }',
            ),
            array(
                '<?php
                    function a($c)
                    {
                        $_SERVER["abc"] = 3;
                        return $_SERVER;
                    }',
            ),
            array(
                '<?php
                    function foo ($bar)
                    {
                        $a = 123;
                        if ($bar)
                            $a = 12345;
                        return $a;
                    }',
            ),
            array(
                '<?php
                    function foo ($bar)
                    {
                        $a = 123;
                        if ($bar)
                            ;
                        else
                            $a = 12345;
                        return $a;
                    }',
            ),
            array(
                '<?php
                    function foo ($bar)
                    {
                        $a = 123;
                        if ($bar)
                            ;
                        elseif($b)
                            $a = 12345;
                        return $a;
                    }',
            ),
            array(
                '<?php
                    function a($c)
                    {
                        $a = 1;
                        echo $a ."=1";
                        return $a;
                    }',
            ),
            array(
                '<?php
                    function a($c)
                    {
                        if ($a = 1)
                            return $a;
                    }',
            ),
            array(
                '<?php
                    function a($c)
                    {
                        $a = 1;
                        $a += 1;
                        return $a;
                    }',
            ),
            array(
                '<?php
                    function a($c)
                    {
                        $d = $c && $a = 1;
                        return $a;
                    }',
            ),
        );
    }
}
