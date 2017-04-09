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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer
 */
final class NativeFunctionCasingFixerTest extends AbstractFixerTestCase
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
        return array(
            array(
                '<?php
                namespace Bar {
                    function STRLEN($str) {
                        return "overriden" . \strlen($str);
                    }
                }

                namespace {
                    echo \Bar\STRLEN("xxx");
                }',
            ),
            array(
                '<?php
                    echo strtolower("hello 1");
                ',
                '<?php
                    echo STRTOLOWER("hello 1");
                ',
            ),
            array(
                '<?php
                    echo strtolower //a
                        ("hello 2");
                ',
                '<?php
                    echo STRTOLOWER //a
                        ("hello 2");
                ',
            ),
            array(
                '<?php
                    echo strtolower /**/   ("hello 3");
                ',
                '<?php
                    echo STRTOLOWER /**/   ("hello 3");
                ',
            ),
            array(
                '<?php
                    echo \sqrt(4);
                ',
                '<?php
                    echo \sQrT(4);
                ',
            ),
            array(
                '<?php
                    echo "1".\sqrt("hello 5");
                ',
                '<?php
                    echo "1".\SQRT("hello 5");
                ',
            ),
            array(
                '<?php
                    class Test{
                        public function gettypE()
                        {
                            return 1;
                        }

                        function sqrT($a)
                        {
                        }
                    }
                ',
            ),
            array(
                '<?php
                    new STRTOLOWER();
                ',
            ),
            array(
                '<?php
                    new \STRTOLOWER();
                ',
            ),
            array(
                '<?php
                    new \A\B\STRTOLOWER();
                ',
            ),
            array(
                '<?php
                    a::STRTOLOWER();
                ',
            ),
            array(
                '<?php
                    $a->STRTOLOWER();
                ',
            ),
        );
    }
}
