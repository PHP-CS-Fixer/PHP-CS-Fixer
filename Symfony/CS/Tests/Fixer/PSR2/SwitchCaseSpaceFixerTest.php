<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * @internal
 */
final class SwitchCaseSpaceFixerTest extends AbstractFixerTestBase
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
                switch ($a) {
                    case 42:
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case false:
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case false:
                        break;
                    default:
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case "prod":
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case "prod" :
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case "prod":
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case "prod"       :
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case 42:
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case 42 :
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case false:
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case false :
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case false:
                        break;
                    default:
                }
                ',
                '<?php
                switch ($a) {
                    case false :
                        break;
                    default :
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case 42:
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case 42    :
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case $b ? "c" : "d":
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case $b ? "c" : "d" :
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case $b ? "c" : "d": break;
                }
                ',
                '<?php
                switch ($a) {
                    case $b ? "c" : "d" : break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case $b ? "c" : "this" ? "is" : "ugly":
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case $b ? "c" : "this" ? "is" : "ugly" :
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case $b ?: $c:
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case $b ?: $c :
                        break;
                }
                ',
            ),
            array(
                '<?php
                $a = 5.1;
                $b = 1.0;
                switch($a) {
                    case (int) $a < 1: {
                        echo "leave alone";
                        break;
                    }
                    case ($a < 2)/* test */: {
                        echo "fix 1";
                        break;
                    }
                    case (3): {
                        echo "fix 2";
                        break;
                    }
                    case /**/(/**/ // test
                        4
                        /**/)//
                        /**/: {
                        echo "fix 3";
                        break;
                    }
                    case (((int)$b) + 4.1): {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2: {
                        echo "leave alone";
                        break;
                    }
                }
                ',
                '<?php
                $a = 5.1;
                $b = 1.0;
                switch($a) {
                    case (int) $a < 1 : {
                        echo "leave alone";
                        break;
                    }
                    case ($a < 2)/* test */ : {
                        echo "fix 1";
                        break;
                    }
                    case (3) : {
                        echo "fix 2";
                        break;
                    }
                    case /**/(/**/ // test
                        4
                        /**/)//
                        /**/ : {
                        echo "fix 3";
                        break;
                    }
                    case (((int)$b) + 4.1) : {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2 : {
                        echo "leave alone";
                        break;
                    }
                }
                ',
                array(
                    '<?php
                    switch ($a) {
                        case 42:
                            break;
                        case 1:
                            switch ($a) {
                                case 42:
                                    break;
                                default:
                                    echo 1   ;
                            }
                    }
                    ',
                    '<?php
                    switch ($a) {
                        case 42   :
                            break;
                        case 1    :
                            switch ($a) {
                                case 42   :
                                    break;
                                default :
                                    echo 1   ;
                            }
                    }
                    ',
                ),
            ),
        );
    }
}
