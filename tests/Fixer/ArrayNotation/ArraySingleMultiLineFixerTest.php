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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\ArraySingleMultiLineFixer
 */
final class ArraySingleMultiLineFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input, ?array $config): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield 'array square brace open' => [
            '<?php $a = [
1,
2,
3
];',
            '<?php $a = [1,2,3];',
            ['element_count' => 3, 'inner_length' => 10000, 'conditions' => 'any'],
        ];

        yield 'long array syntax' => [
            '<?php $b = array(
1,
2,
3
);',
            '<?php $b = array(1,2,3);',
            ['element_count' => 3, 'inner_length' => 2, 'conditions' => 'all'],
        ];

        yield 'only touch arrays' => [
            '<?php
                $a1 = [1,2,3,4,5,6,7,8,9];
                $b1 = [
1,
2,
3,
4,
5,
6,
7,
8,
9,
10
];

                $c1 = ["abcdefghijklmnopqrstuv"];
                $d1 = [
"abcdefghijklmnopqrstuvw"
];

                list($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s) = foo1();
                function foo($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s){}
                [$a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s] = foo2();
            ',
            '<?php
                $a1 = [1,2,3,4,5,6,7,8,9];
                $b1 = [1,2,3,4,5,6,7,8,9,10];

                $c1 = ["abcdefghijklmnopqrstuv"];
                $d1 = ["abcdefghijklmnopqrstuvw"];

                list($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s) = foo1();
                function foo($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s){}
                [$a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r,$s] = foo2();
            ',
            ['element_count' => 10, 'inner_length' => 25],
        ];

        yield 'conditions `all` with one not matched' => [
            '<?php $b2 = [1,2,3,4,5,6,7,8,9,10,11];',
            null,
            ['element_count' => 2, 'inner_length' => 20000, 'conditions' => 'all'],
        ];

        yield 'array square brace open, trailing `,`' => [
            '<?php $a5 = [
1,
2,
3,
4,     /* 0 */
5,/* 1 */ /* 2 */
6,/* 6+ */
7,
];',
            '<?php $a5 = [1,2,3,4,     /* 0 */5,/* 1 */ /* 2 */  6,/* 6+ */7,];',
            ['element_count' => 3, 'inner_length' => 10000, 'conditions' => 'any'],
        ];

        yield 'array square brace open, simple nested' => [
            '<?php $a6 = [
1,
[2],
3
];',
            '<?php $a6 = [1,[2],3];',
            ['element_count' => 3, 'inner_length' => 10000, 'conditions' => 'any'],
        ];

        yield 'array square brace open, double, nested' => [
            '<?php $a7 = [
1,
[
11,
12,
13
],
3
];',
            '<?php $a7 = [1,[11,12,13],3];',
            ['element_count' => 3, 'inner_length' => 10000, 'conditions' => 'any'],
        ];

        yield 'minimal not match' => [
            '<?php
$a = [];
$b = [];
$c = array();
',
            null,
            ['element_count' => 1, 'inner_length' => 1, 'conditions' => 'any'],
        ];

        yield 'no condition match' => [
            '<?php
$a = [1];
$b = [2];
$c = array(1);
',
            null,
            ['element_count' => 2, 'inner_length' => 100000000, 'conditions' => 'any'],
        ];

        yield 'mix match' => [
            '<?php
$a = [
1111
];
$b = [
2222
];
$c = array(
3333
);

$e = [1];
',
            '<?php
$a = [1111];
$b = [2222];
$c = array(3333);

$e = [1];
',
            ['element_count' => 1, 'inner_length' => 3, 'conditions' => 'all'],
        ];

        yield [
            '<?php
                $a = array(
[
[
[
1
],
[
2
]
],
[
3,
[
4,
8,
[
7
]
],
5
]
]
);
            ',
            '<?php
                $a = array([[[1],[2]],[3,[4,8,[7]],5]]);
            ',
            ['element_count' => 1, 'conditions' => 'any'],
        ];

        yield 'array is already multiline' => [
            "<?php
class Foo {
    private static array \$tags = [
        'api', 'author', 'category', 'copyright', 'deprecated', 'example',
        'global', 'internal', 'license', 'link', 'method', 'package', 'param',
        'property', 'property-read', 'property-write', 'return', 'see',
        'since', 'subpackage', 'throws', 'todo', 'uses', 'var', 'version',
    ];
}
",
            null,
            ['element_count' => 1, 'inner_length' => 1, 'conditions' => 'any'],
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input, ?array $config): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixPre80Cases(): iterable
    {
        yield 'array index curly brace open' => [
            '<?php $a = [
1,
2,
3
];',
            '<?php $a = [1,2,3];',
            ['element_count' => 3, 'conditions' => 'any'],
        ];
    }
}
