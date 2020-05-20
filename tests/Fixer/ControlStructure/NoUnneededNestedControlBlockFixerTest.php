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
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUnneededNestedControlBlockFixer
 */
final class NoUnneededNestedControlBlockFixerTest extends AbstractFixerTestCase
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
        // fix cases

        yield 'simple "if" case' => [
            '<?php
if ($foo && $bar) {



}
',
            '<?php
if ($foo) {
    if ($bar) {

    }
}
',
        ];

        yield 'simple "if" case over multiple lines' => [
            '<?php
if ($foo && $bar) {





}
',
            '<?php
if ($foo) {
 if
  ($bar)
    {

                  }
}
',
        ];

        yield 'simple "if" case single line' => [
            '<?php if($foo && $bar){}',
            '<?php if($foo){if($bar){}}',
        ];

        yield 'simple "elseif" case' => [
            '<?php
if ($foo22) {

} elseif($c && $bar) {



}
',
            '<?php
if ($foo22) {

} elseif($c) {
    if ($bar)
    {
    }
}
',
        ];

        yield 'simple "else" case' => [
            '<?php
if ($foo2) {

} elseif ($bar) {



}
',
            '<?php
if ($foo2) {

} else {
    if ($bar)
    {
    }
}
',
        ];

        yield 'simple "if" comment case I' => [
            '<?php
{
    if ($foo1 && $bar1) { # 5


 # 3
    } # 2
} # 1',
            '<?php
{
    if ($foo1) { # 5
        if ($bar1) {

        } # 3
    } # 2
} # 1',
        ];

        yield '"if" comment case II' => [
            '<?php
if (1 + /* 1 */ $foo && $bar + /* 2 */ 99) {



}
',
            '<?php
if (1 + /* 1 */ $foo) {
    if ($bar + /* 2 */ 99) {

    }
}
',
        ];

        yield 'simple "else" case II' => [
            '<?php
if ($foo) {

} elseif ($bar && $c) {

        echo 1;
}
',
            '<?php
if ($foo) {

} else {
    if ($bar && $c) {
        echo 1; }
}
',
        ];

        yield 'simple "else" case III' => [
            '<?php
if ($foo) {

} elseif ($bar && $c) {

        echo 1;
        echo 2;
        echo 3;

}
',
            '<?php
if ($foo) {

} else {
    if ($bar && $c) {
        echo 1;
        echo 2;
        echo 3;
    }
}
',
        ];

        yield 'comment "else" case' => [
            '<?php
if ($foo) {

} elseif (/* 2 */$bar) {
    /* 1 *//* 3 */ /* 4 */
        echo 1;

}
',
            '<?php
if ($foo) {

} else {
    /* 1 */ if (/* 2 */$bar)/* 3 */ { /* 4 */
        echo 1;
    }
}
',
        ];

        // precedence cases

        yield '"if" case precedence "and"' => [
            '<?php
if (($foo and $c) && $bar) {



}
',
            '<?php
if ($foo and $c) {
    if ($bar) {

    }
}
',
        ];

        $precedenceCases = [
            '$x && ($y %= $b)' => '$y %= $b',
            '$x && ($y &= $b)' => '$y &= $b',
            '$x && ($y **= $b)' => '$y **= $b',
            '$x && ($y *= $b)' => '$y *= $b',
            '$x && ($y += $b)' => '$y += $b',
            '$x && ($y -= $b)' => '$y -= $b',
            '$x && ($y .= $b)' => '$y .= $b',
            '$x && ($y /= $b)' => '$y /= $b',
            '$x && ($y <<= $b)' => '$y <<= $b',
            '$x && ($y = $b)' => '$y = $b',
            '$x && ($y >>= $b)' => '$y >>= $b',
            '$x && ($y ? $a : $b)' => '$y ? $a : $b',
            '$x && ($y ^= $b)' => '$y ^= $b',
            '$x && ($y and $b)' => '$y and $b',
            '$x && ($y or $b)' => '$y or $b',
            '$x && ($y xor $b)' => '$y xor $b',
            '$x && ($y |= $b)' => '$y |= $b',
            '$x && ($y || $b)' => '$y || $b',
        ];

        foreach ($precedenceCases as $combined => $second) {
            yield [
                sprintf(
                    '<?php
function foo(){
if (%s) {



}
}
',
                    $combined
                ),
                sprintf(
                    '<?php
function foo(){
if ($x) {
    if (%s) {

    }
}
}
',
                    $second
                ),
            ];
        }

        yield 'already wrapped precedence' => [
            '<?php
if ($foo && $a && ($a xor $b)) {



}
',
            '<?php
if ($foo) {
    if ($a && ($a xor $b)) {

    }
}
',
        ];

        // multiple flat

        $expected = str_repeat("if (\$foo && \$bar) {\n\n# 1\n\n}\n", 10);
        $input = str_repeat("if (\$foo) {\n    if (\$bar) {\n# 1\n    }\n}\n", 10);

        yield 'multiple flat "if" case' => [
            "<?php\n".$expected,
            "<?php\n".$input,
        ];

        // multiple nested

        yield 'multiple "if" case' => [
            '<?php
if ($foo && $bar1 && $bar2 && $bar3) {







}
',
            '<?php
if ($foo) {
    if ($bar1) {
        if ($bar2) {
            if ($bar3) {

            }
        }
    }
}
',
        ];

        yield 'multiple "else" case' => [
            '<?php
if ($foo) {

} else {
    if ($bar) {

    } else {
        if ($bar1) {

        } elseif ($bar2) {


        }
    }
}
',
            '<?php
if ($foo) {

} else {
    if ($bar) {

    } else {
        if ($bar1) {

        } else {
            if ($bar2) {
            }
        }
    }
}
',
        ];

        yield 'multiple "elseif" case' => [
            '<?php
if ($foo) {

} elseif($c && $bar) {

        if ($foo1) {

        } elseif($c1 && $bar1) {

                if ($foo2) {

                } elseif($c2 && $bar2) {



                }

        }

}
',
            '<?php
if ($foo) {

} elseif($c) {
    if ($bar) {
        if ($foo1) {

        } elseif($c1) {
            if ($bar1) {
                if ($foo2) {

                } elseif($c2) {
                    if ($bar2) {

                    }
                }
            }
        }
    }
}
',
        ];

        // do not fix cases

        yield 'alternative syntax' => [
            '<?php
    if ($a) echo 1;

    if (true) :
        $foo = 0;
    endif;

    if (true):
        echo 1;
    else:
        echo 2;
    endif;

    if ($bar):
        echo 1;
    elseif ($foo):
        echo 2;
    endif;
',
        ];

        yield [
            '<?php
if ($foo) {

} else {
echo 1;
    if ($bar) {

    }
}
',
        ];

        yield [
            '<?php
if ($foo) {

} else {
    if ($bar) {

    }
    echo 1;
}
',
        ];

        yield [
            '<?php

if ($foo) {
    if ($bar) {

    }
} else {
    echo 1;
}',
        ];

        yield [
            '<?php

if ($foo) {
    if ($bar) {

    }
} elseif ($a) {
    echo 1;
}',
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix70Cases
     * @requires PHP 7.0
     */
    public function testFix70($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix70Cases()
    {
        yield [
            '<?php if ($x && ($y ?? $b1)) {}',
            '<?php if ($x) { if($y ?? $b1){}}',
        ];

        yield [
            '<?php if ($x && ($y = yield $b)) {}',
            '<?php if ($x) { if($y = yield $b){}}',
        ];

        yield [
            '<?php if ($x && ($y = yield from $b)) {}',
            '<?php if ($x) { if($y = yield from $b){}}',
        ];

        yield 'right side precedence' => [
            '<?php
function foo () {
    if ($a) {

    } elseif ($b = yield $f1 || $e = $c += 1) {



    }
}',
            '<?php
function foo () {
    if ($a) {

    } else {
        if ($b = yield $f1 || $e = $c += 1) {

        }
    }
}',
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFix74Cases
     * @requires PHP 7.4
     */
    public function testFix74($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix74Cases()
    {
        yield [
            '<?php if ($x && ($y ??= $b1)) {}',
            '<?php if ($x) { if($y ??= $b1){}}',
        ];
    }
}
