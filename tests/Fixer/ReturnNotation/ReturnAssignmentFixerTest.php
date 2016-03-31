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
                '<?php return (1 + 2 + $b); ',
                '<?php $a= (1 + 2 + $b);return $a;',
            ),
            array(
                '<?php return 1;  ?>',
                '<?php $b=1;return $b ?>',
            ),
            array(
                '<?php return 2;   ?>',
                '<?php $c=2; return $c ?>',
            ),
            array(
                '<?php return  3;   ?>',
                '<?php $c = 3; return $c ?>',
            ),
            array(
                '<?php if ($c > 4){}   return 3;   ?>',
                '<?php if ($c > 4){} $c = 3; return $c ?>',
            ),
            array(
                '<?php
                    if ($c) {
                          return 0;
                         '.'
                    }
                      return testFunction(123+1);
                     '.'
                ',
                '<?php
                    if ($c) {
                        $b = 0;
                        return $b;
                    }
                    $a = testFunction(123+1);
                    return $a;
                ',
            ),
            // do not fix the cases below
            array(
                '<?php $a1=1;return $a;',
            ),
            array(
                '<?php $a=1;return $a + 1;',
            ),
            array(
                '<?php
                    $_SERVER["abc"] = 3;
                    return $_SERVER;
                ',
            ),
            array('
                <?php
                    static $b, $a = 1;
                    return $a
                ?>
                ',
            ),
            array('
                <?php
                    $d = $c && $a = 1;
                    return $a;
                ',
            ),
            array('
                <?php
                    static $a = 1;
                    return $a;
                ',
            ),
            array('
                <?php
                    $a = 1;
                    $a += 1;
                    return $a;
                ',
            ),
            array('
                <?php
                    if ($a = 1)
                        return $a;
                ',
            ),
            array('
                <?php
                    function foo ($bar)
                    {
                        $a = 123;
                        if ($bar)
                            $a = 12345;
                        return $a;
                    }',
            ),
            array('
                <?php
                    echo $a;
                    return $a;
                ',
            ),
            array('
                <?php
                    $a = 1;
                ?>
                <?php
                    return $a;
                ',
            ),
            array('
                <?php
                    $a = 1
                ?>
                <?php
                    ;
                    return $a;
                ',
            ),
            array('
                <?php
                    $a = 1;
                ?>
                <?php
                    ;
                    return $a;
                ',
            ),
        );
    }
}
