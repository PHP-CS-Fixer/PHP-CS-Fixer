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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\SwitchContinueToBreakFixer
 */
final class SwitchContinueToBreakFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'alternative syntax |' => [
            <<<'EOD'
                <?php
                                    switch($foo):
                                        case 3:
                                            continue;
                                    endswitch?>
                EOD."\n                ",
        ];

        yield 'alternative syntax ||' => [
            <<<'EOD'
                <?php

                foreach ([] as $v) {
                    continue;
                }

                if ($foo != 0) {
                }

                switch ($foo):
                endswitch;
                EOD,
        ];

        yield 'nested switches' => [
            <<<'EOD'
                <?php
                switch($z) {
                    case 1:
                        switch($x) {
                            case 2:
                                switch($y) {
                                    case 3:
                                        switch($z) {
                                            case 4:
                                                break; // z.1
                                        }
                                        break; // z
                                }
                                break; // y
                        }
                        break; // x
                }

                EOD,
            <<<'EOD'
                <?php
                switch($z) {
                    case 1:
                        switch($x) {
                            case 2:
                                switch($y) {
                                    case 3:
                                        switch($z) {
                                            case 4:
                                                continue; // z.1
                                        }
                                        continue; // z
                                }
                                continue; // y
                        }
                        continue; // x
                }

                EOD,
        ];

        yield 'nested 2' => [
            <<<'EOD'
                <?php
                while ($foo) {
                    switch ($bar) {
                        case "baz":
                            while ($xyz) {
                                switch($zA) {
                                    case 1:
                                        break 3; // fix
                                }

                                if ($z) continue;
                                if ($zz){ continue; }

                                if ($zzz) continue 3;
                                if ($zzz){ continue 3; }

                                if ($b) break 2; // fix
                            }

                            switch($zG) {
                                case 1:
                                    switch($zF) {
                                        case 1:
                                            break 1; // fix
                                        case 2:
                                            break 2; // fix
                                        case 3:
                                            break 3; // fix
                                        case 4:
                                            while($a){
                                                while($a){
                                                    while($a){
                                                        if ($a) {
                                                            break 4; // fix
                                                        } elseif($z) {
                                                            break 5; // fix
                                                        } else {
                                                            break 6; // fix
                                                        }

                                                        continue 7;
                                                    }
                                                }
                                            }

                                            continue 4;
                                    }

                                    break 2; // fix
                            }
                    }
                }

                EOD,
            <<<'EOD'
                <?php
                while ($foo) {
                    switch ($bar) {
                        case "baz":
                            while ($xyz) {
                                switch($zA) {
                                    case 1:
                                        continue 3; // fix
                                }

                                if ($z) continue;
                                if ($zz){ continue; }

                                if ($zzz) continue 3;
                                if ($zzz){ continue 3; }

                                if ($b) continue 2; // fix
                            }

                            switch($zG) {
                                case 1:
                                    switch($zF) {
                                        case 1:
                                            continue 1; // fix
                                        case 2:
                                            continue 2; // fix
                                        case 3:
                                            continue 3; // fix
                                        case 4:
                                            while($a){
                                                while($a){
                                                    while($a){
                                                        if ($a) {
                                                            continue 4; // fix
                                                        } elseif($z) {
                                                            continue 5; // fix
                                                        } else {
                                                            continue 6; // fix
                                                        }

                                                        continue 7;
                                                    }
                                                }
                                            }

                                            continue 4;
                                    }

                                    continue 2; // fix
                            }
                    }
                }

                EOD,
        ];

        yield 'nested do while' => [
            <<<'EOD'
                <?php
                switch ($a) {
                    case 1:
                        do {
                            switch ($a) {
                                case 1:
                                    do {
                                        switch ($a) {
                                            case 1:
                                                do {
                                                    continue;
                                                } while (false);

                                            break;
                                        }

                                        continue;
                                    } while (false);

                                break;
                            }
                            continue;
                        } while (false);

                    break;
                }

                EOD,
            <<<'EOD'
                <?php
                switch ($a) {
                    case 1:
                        do {
                            switch ($a) {
                                case 1:
                                    do {
                                        switch ($a) {
                                            case 1:
                                                do {
                                                    continue;
                                                } while (false);

                                            continue;
                                        }

                                        continue;
                                    } while (false);

                                continue;
                            }
                            continue;
                        } while (false);

                    continue;
                }

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                switch(foo()) {
                    case 1: while(bar($i))continue;break;
                    default: echo 7;
                }

                EOD,
            <<<'EOD'
                <?php
                switch(foo()) {
                    case 1: while(bar($i))continue;continue;
                    default: echo 7;
                }

                EOD,
        ];

        yield 'do not fix cases' => [
            <<<'EOD'
                <?php
                switch($a) {
                    case 1:
                        while (false) {
                            continue;
                        }

                        while (false) break 1;

                        do {
                            continue;
                        } while (false);

                        for ($a = 0; $a < 1; ++$a) {
                            continue;
                        }

                        foreach ($a as $b) continue;
                        for (; $i < 1; ++$i) break 1; echo $i;
                        for (;;) continue;
                        while(false) continue;
                        while(false) continue?><?php

                        // so bad and such a mess, not worth adding a ton of logic to fix this
                        switch($z) {
                            case 1:
                                continue ?>   <?php 23;
                            case 2:
                                continue 1?>   <?php + $a;
                        }
                }

                EOD,
        ];

        yield 'nested while, do not fix' => [
            <<<'EOD'
                <?php
                switch(foo()) {
                    case 1: while(bar($i)){ --$i; echo 1; continue;}break;
                    default: echo 8;
                }
                EOD,
        ];

        yield 'not int cases' => [
            <<<'EOD'
                <?php
                while($b) {
                switch($a) {
                case 1:
                    break 01;
                case 2:
                    break 0x1;
                case 22:
                    break 0x01;
                case 3:
                    break 0b1;
                case 32:
                    break 0b0001;
                case 4:
                    break 0b000001;
                // do not fix
                case 1:
                    continue 02;
                case 2:
                    continue 0x2;
                case 3:
                    continue 0b10;
                }
                }
                EOD,
            <<<'EOD'
                <?php
                while($b) {
                switch($a) {
                case 1:
                    continue 01;
                case 2:
                    continue 0x1;
                case 22:
                    continue 0x01;
                case 3:
                    continue 0b1;
                case 32:
                    continue 0b0001;
                case 4:
                    continue 0b000001;
                // do not fix
                case 1:
                    continue 02;
                case 2:
                    continue 0x2;
                case 3:
                    continue 0b10;
                }
                }
                EOD,
        ];

        yield 'deep nested case' => [
            <<<'EOD'
                <?php
                switch ($a) {
                case $b:
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    switch ($a) {
                        case 1:
                            echo 1;

                            break 10;
                }}}}}}}}}}
                EOD,
            <<<'EOD'
                <?php
                switch ($a) {
                case $b:
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    while (false) {
                    switch ($a) {
                        case 1:
                            echo 1;

                            continue 10;
                }}}}}}}}}}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                            switch($a) {
                                case "a":
                                    echo __FILE__;
                                    break;
                            }
                EOD."\n            ",
            <<<'EOD'
                <?php
                            switch($a) {
                                case "a":
                                    echo __FILE__;
                                    continue;
                            }
                EOD."\n            ",
            'numeric literal separator' => [
                <<<'EOD'
                    <?php
                    switch ($a) {
                    case $b:
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        switch ($a) {
                            case 1:
                                echo 1;

                                break 1_0;
                    }}}}}}}}}}
                    EOD,
                <<<'EOD'
                    <?php
                    switch ($a) {
                    case $b:
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        while (false) {
                        switch ($a) {
                            case 1:
                                echo 1;

                                continue 1_0;
                    }}}}}}}}}}
                    EOD,
            ],
        ];
    }
}
