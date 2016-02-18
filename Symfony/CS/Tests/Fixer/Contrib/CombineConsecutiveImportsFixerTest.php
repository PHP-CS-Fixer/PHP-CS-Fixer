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
final class CombineConsecutiveImportsFixerTest extends AbstractFixerTestBase
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
                    global $a, $b;
                     '.'
                ',
                '<?php
                    global $a;
                    global $b;
                ',
            ),
            array(
                '<?php
                    global $a, $b;
                     '.'
                    // global $t
                    $global = 1;
                ',
                '<?php
                    global $a;
                    global $b;
                    // global $t
                    $global = 1;
                ',
            ),
            array(
                '<?php
                    global $a, $b, $c, $d, $e;
                     '.'
                    /**/
                     '.'
                    //
                     '.'
                ',
                '<?php
                    global $a;
                    global $b;
                    /**/
                    global $c;
                    //
                    global $d, $e;
                ',
            ),
            array(
                '<?php
                    function test() {
                        global $a, $b ;
                         ?>test
                        test<?php
                    }
                ',
                '<?php
                    function test() {
                        global $a;
                        global $b ?>test
                        test<?php
                    }
                ',
            ),
            array(
                '<?php
                    global $a
                ?>
                    test

                <?php
                    global $b;
                ',
            ),
        );
    }
}
