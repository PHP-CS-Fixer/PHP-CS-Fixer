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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\FunctionNotation\ImplodeCallFixer>
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\ImplodeCallFixer
 *
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class ImplodeCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ["<?php implode('', [1,2,3]);"];

        yield ['<?php implode("", $foo);'];

        yield ['<?php implode($foo, $bar);'];

        yield ['<?php $arrayHelper->implode($foo);'];

        yield ['<?php ArrayHelper::implode($foo);'];

        yield ['<?php ArrayHelper\implode($foo);'];

        yield ['<?php define("implode", "foo"); implode; bar($baz);'];

        yield [
            '<?php implode("", $foo);',
            '<?php implode($foo, "");',
        ];

        yield [
            '<?php \implode("", $foo);',
            '<?php \implode($foo, "");',
        ];

        yield [
            '<?php implode("Lorem ipsum dolor sit amet", $foo);',
            '<?php implode($foo, "Lorem ipsum dolor sit amet");',
        ];

        yield [
            '<?php implode(\'\', $foo);',
            '<?php implode($foo);',
        ];

        yield [
            '<?php IMPlode("", $foo);',
            '<?php IMPlode($foo, "");',
        ];

        yield [
            '<?php implode("",$foo);',
            '<?php implode($foo,"");',
        ];

        yield [
            '<?php implode("", $weirdStuff[mt_rand($min, getMax()) + 200]);',
            '<?php implode($weirdStuff[mt_rand($min, getMax()) + 200], "");',
        ];

        yield [
            '<?php
                implode(
                    "",
                    $foo
                );',
            '<?php
                implode(
                    $foo,
                    ""
                );',
        ];

        yield [
            '<?php
                implode(
                    \'\', $foo
                );',
            '<?php
                implode(
                    $foo
                );',
        ];

        yield [
            '<?php
implode(# 1
""/* 2.1 */,# 2.2
$foo# 3
);',
            '<?php
implode(# 1
$foo/* 2.1 */,# 2.2
""# 3
);',
        ];

        yield [
            '<?php
implode(# 1
# 2
\'\', $foo# 3
# 4
)# 5
;',
            '<?php
implode(# 1
# 2
$foo# 3
# 4
)# 5
;',
        ];

        yield [
            '<?php
implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);
// comment for testing
implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);implode(\'\', $a);
',
            '<?php
implode($a);implode($a);implode($a);implode($a);implode($a);implode($a);
// comment for testing
implode($a);implode($a);implode($a);implode($a);implode($a);implode($a);
',
        ];

        yield [
            '<?php implode("", $foo, );',
            '<?php implode($foo, "", );',
        ];

        yield [
            '<?php implode(\'\', $foo, );',
            '<?php implode($foo, );',
        ];

        yield [
            '<?php
                implode(
                    "",
                    $foo,
                );',
            '<?php
                implode(
                    $foo,
                    "",
                );',
        ];
    }
}
