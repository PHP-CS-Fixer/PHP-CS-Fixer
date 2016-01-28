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
final class NoUselessContinueCountFixerTest extends AbstractFixerTestBase
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
                    while(true) {
                        break  ;
                    }
                    while(true) {
                        continue ;
                    }
                ',
                '<?php
                    while(true) {
                        break 1 ;
                    }
                    while(true) {
                        continue 1;
                    }
                ',
            ),
            array(
                '<?php
                    while(true) {
                        while(true) {
                            while(true) {
                                while(true) {
                                    break 3;
                                }
                                while(true) {
                                    break   2 /*1*/;
                                }
                            }
                        }
                    }
                    while(true) {
                        break; // 1
                    }
                    while(true) {
                        continue; // 1
                    }
                    while(true) {
                        continue   ; // 1
                    }
                ',
            ),
            array(
                '<?php
                    while(false)
                        break?><?php $a = 1 + 2;
                ',
            ),
        );
    }

    /**
     * @dataProvider provide54FixCases
     */
    public function test54Fix($expected, $input = null)
    {
        if (PHP_VERSION_ID >= 50400) {
            $this->markTestSkipped('PHP 5.3 (or older) is required.');
        }

        $this->makeTest($expected, $input);
    }

    public function provide54FixCases()
    {
        return array(
            array(
                '<?php
                    while(true) {
                        break  ;
                    }
                    while(true) {
                        continue ;
                    }
                    while(true) {
                        continue 0+5;
                    }
                    while(true) {
                        continue 1+$a;
                    }
                    while(true) {
                        continue $c;
                    }
                ',
                '<?php
                    while(true) {
                        break 0 ;
                    }
                    while(true) {
                        continue 0;
                    }
                    while(true) {
                        continue 0+5;
                    }
                    while(true) {
                        continue 1+$a;
                    }
                    while(true) {
                        continue $c;
                    }
                ',
            ),
        );
    }
}
