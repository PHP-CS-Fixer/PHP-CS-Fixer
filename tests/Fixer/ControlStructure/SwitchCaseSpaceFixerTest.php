<?php

declare(strict_types=1);

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
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer
 */
final class SwitchCaseSpaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        yield from [
            [
                '<?php
    switch (1) {
        case (1 #
)#
 :
         echo 1;
    }
?>
',
            ],
            [
                '<?php
    switch (1) {
        case 1 #
            : echo 1;
    }
?>
',
            ],
            [
                '<?php
                switch ($a) {
                    case 42:
                        break;
                }
                ',
            ],
            [
                '<?php
                switch ($a) {
                    case false:
                        break;
                }
                ',
            ],
            [
                '<?php
                switch ($a) {
                    case false:
                        break;
                    default:
                }
                ',
            ],
            [
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
            ],
            [
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
                    case 42 :
                        break;
                }
                ',
            ],
            [
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
            ],
            [
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
                    case 42    :
                        break;
                }
                ',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php
                $a = 5.1;
                $b = 1.0;
                switch($a) {
                    case (int) $a < 1: {
                        echo "leave alone";
                        break;
                    }
                    case ($a < 2)/* test */ : {
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
                        /**/ : {
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
                [
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
                ],
            ],
            [
                '<?php
                    switch($foo) {
                        case 4:  ; ;
                        case 31 + test(";");  ; ; ;;
                        case 1 + test(";"); // ;
                        case (1+2/*;*/);
                        case 1;
                        case 2;
                            return 1;
                        default;
                            return 2;
                }',
                '<?php
                    switch($foo) {
                        case 4  :  ; ;
                        case 31 + test(";") ;  ; ; ;;
                        case 1 + test(";") ; // ;
                        case (1+2/*;*/) ;
                        case 1  ;
                        case 2 ;
                            return 1;
                        default ;
                            return 2;
                }',
            ],
        ];

        if (\PHP_VERSION_ID < 80000) {
            yield [
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
            ];
        }
    }
}
