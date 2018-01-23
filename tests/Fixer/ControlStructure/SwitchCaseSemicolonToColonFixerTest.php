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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer
 */
final class SwitchCaseSemicolonToColonFixerTest extends AbstractFixerTestCase
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
        return [
            [
                '<?php
                switch (1) {
                    case f(function () { return; }):
                        break;
                }
                ',
                '<?php
                switch (1) {
                    case f(function () { return; });
                        break;
                }
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                switch ($a) {
                    case 42:;;// NoEmptyStatementFixer should clean this up (partly)
                        break;
                }
                ',
                '<?php
                switch ($a) {
                    case 42;;;// NoEmptyStatementFixer should clean this up (partly)
                        break;
                }
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        return [
            'nested switch in switch case' => [
                '<?php
                    switch (1) {
                        case new class {public function A(){echo 1;switch(time()){case 1: echo 2;}}}:break;}
                ',
                '<?php
                    switch (1) {
                        case new class {public function A(){echo 1;switch(time()){case 1; echo 2;}}};break;}
                ',
            ],
            [
                '<?php
                switch (1) {
                    case $b ? f(function () { return; }) : new class {public function A(){echo 1;}} :
                        break;
                }
                ',
                '<?php
                switch (1) {
                    case $b ? f(function () { return; }) : new class {public function A(){echo 1;}} ;
                        break;
                }
                ',
            ],
        ];
    }
}
