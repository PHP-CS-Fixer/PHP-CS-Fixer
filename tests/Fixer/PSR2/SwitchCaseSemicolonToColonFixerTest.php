<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\PSR2;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class SwitchCaseSemicolonToColonFixerTest extends AbstractFixerTestCase
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
                switch ($a) {
                    case 42:
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case 42;
                        break;
                }
                ',
            ),
            array(
                '<?php
                    switch ($a) {
                        case 42:
                            break;
                        case 1:
                            switch ($a) {
                                case 42:
                                    break;
                                default :
                                    echo 1;
                            }
                    }',
                '<?php
                    switch ($a) {
                        case 42;
                            break;
                        case 1:
                            switch ($a) {
                                case 42;
                                    break;
                                default ;
                                    echo 1;
                            }
                    }',
            ),
            array(
                '<?php
                switch ($a) {
                    case 42:;;// NoDuplicateSemicolonsFixer should clean this up (partly)
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case 42;;;// NoDuplicateSemicolonsFixer should clean this up (partly)
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch ($a) {
                    case $b ? "c" : "d" :
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case $b ? "c" : "d" ;
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
                    case $b ? "c" : "d"; break;
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
                    case $b ? "c" : "this" ? "is" : "ugly";
                        break;
                }
                ',
            ),
            array(
                '<?php
                switch($a) {
                    case (int) $a < 1: {
                        echo "leave ; alone";
                        break;
                    }
                    case ($a < 2)/* test */ : {
                        echo "fix 1";
                        break;
                    }
                    case (3):{
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
                    case (((int)$b) + 4.1) : {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2 : {;;
                        echo "leave alone";
                        break;
                    }
                }
                ',
                '<?php
                switch($a) {
                    case (int) $a < 1; {
                        echo "leave ; alone";
                        break;
                    }
                    case ($a < 2)/* test */ ; {
                        echo "fix 1";
                        break;
                    }
                    case (3);{
                        echo "fix 2";
                        break;
                    }
                    case /**/(/**/ // test
                        4
                        /**/)//
                        /**/; {
                        echo "fix 3";
                        break;
                    }
                    case (((int)$b) + 4.1) ; {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2 ; {;;
                        echo "leave alone";
                        break;
                    }
                }
                ',
            ),
        );
    }
}
