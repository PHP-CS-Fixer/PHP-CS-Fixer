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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\SwitchContinueToBreakFixer
 */
final class SwitchContinueToBreakFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            'simple case' => [
                '<?php
switch($a) {
    case 1:
        echo 1;

        break;
    case 2:
        echo 2;

        if ($z) break 1;

        break 1;
    case 3:
        echo 2;

        continue 2;
    case 4:
        continue 12;
}
',
                '<?php
switch($a) {
    case 1:
        echo 1;

        continue;
    case 2:
        echo 2;

        if ($z) continue 1;

        continue 1;
    case 3:
        echo 2;

        continue 2;
    case 4:
        continue 12;
}
',
            ],
            'nested switches' => [
                '<?php
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
',
                '<?php
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
',
            ],
            'nested 1' => [
                '<?php
    while($a) {
        switch($b) {
            case 1:
                break 1;
            case 2:
                break 1;
            case 3:
                continue 2;
            case 4:
                continue 3;
        }
    }

    switch($b) {
        case 1:
            switch($c) {
                case 1:
                    break 2;
        }
    }
',
                '<?php
    while($a) {
        switch($b) {
            case 1:
                continue 1;
            case 2:
                continue 1;
            case 3:
                continue 2;
            case 4:
                continue 3;
        }
    }

    switch($b) {
        case 1:
            switch($c) {
                case 1:
                    continue 2;
        }
    }
',
            ],
            'nested 2' => [
                '<?php
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
',
                '<?php
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
',
            ],
            'nested do while' => [
                '<?php
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
',
                '<?php
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
',
            ],
            [
                '<?php
switch(foo()) {
    case 1: while(bar($i))continue;break;
    default: echo 7;
}
',
                '<?php
switch(foo()) {
    case 1: while(bar($i))continue;continue;
    default: echo 7;
}
',
            ],
            'do not fix cases' => [
                '<?php
switch($a) {
    case 1:
        while (false) {
            continue;
        }

        while (false) continue 2;

        do {
            continue;
        } while (false);

        for ($a = 0; $a < 1; ++$a) {
            continue;
        }

        foreach ($a as $b) continue;
        for (; $i < 1; ++$i) continue 7; echo $i;
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
',
            ],
            'nested while, do not fix' => [
                '<?php
switch(foo()) {
    case 1: while(bar($i)){ --$i; echo 1; continue;}break;
    default: echo 8;
}',
            ],
            'not int cases' => [
                '<?php
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
}',
                '<?php
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
}',
            ],
            'deep nested case' => [
                '<?php
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
}}}}}}}}}}',
                '<?php
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
}}}}}}}}}}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixPhp70Cases
     * @requires PHP 7.0
     */
    public function testFixPhp70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixPhp70Cases()
    {
        return [
            [
                sprintf('<?php switch($a){ case 1: continue 1%d;}', PHP_INT_MAX),
            ],
            [
                '<?php
switch($a) {
    case 1:
        continue $a;
    case 2:
        break 0;
    case 3:
        continue 1.2;
}
',
                '<?php
switch($a) {
    case 1:
        continue $a;
    case 2:
        continue 0;
    case 3:
        continue 1.2;
}
',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.4
     *
     * @dataProvider provideFix74Cases
     */
    public function testFix74($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix74Cases()
    {
        return [
            'numeric literal separator' => [
                '<?php
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
}}}}}}}}}}',
                '<?php
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
}}}}}}}}}}',
            ],
        ];
    }
}
