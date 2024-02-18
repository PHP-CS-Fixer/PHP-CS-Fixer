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

use PhpCsFixer\Fixer\ControlStructure\SingleExpressionPerLineFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vincent Langlet
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\SingleExpressionPerLineFixer
 */
final class SingleExpressionPerLineFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php $a = [1, 2, 3];'];

        yield [
            '<?php
                    $a = [
1,
                        2
];',
            '<?php
                    $a = [1,
                        2];',
        ];

        yield [
            '<?php
                    $a = [
                        1,
2,
                        3
                    ];',
            '<?php
                    $a = [
                        1, 2,
                        3
                    ];',
        ];

        yield ['<?php $a = array(1, 2, 3);'];

        yield [
            '<?php
                    $a = array(
                        1,
2,
                        3
                    );',
            '<?php
                    $a = array(
                        1, 2,
                        3
                    );',
        ];

        yield ['<?php $a = foo(1, 2, 3);'];

        yield [
            '<?php
                    $a = foo(1, 2,
                        3
                    );',
        ];

        yield [
            '<?php
                    $a = foo(
1,
2,
                        3
                    );',
            '<?php
                    $a = foo(1, 2,
                        3
                    );',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_ARGUMENTS]],
        ];

        yield [
            '<?php
                    function(
$a,
$b,
                        $c
                    ) {};',
            '<?php
                    function($a, $b,
                        $c
                    ) {};',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_PARAMETERS]],
        ];

        yield [
            '<?php
                    foreach (
$foo
                    as $key => $value
) {
                    };',
            '<?php
                    foreach ($foo
                    as $key => $value) {
                    };',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_CONTROL_STRUCTURES]],
        ];

        yield [
            '<?php
                    for (
$i = 0;
                    $i < 2;
$i++
) {
                    };',
            '<?php
                    for ($i = 0;
                    $i < 2; $i++) {
                    };',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_CONTROL_STRUCTURES]],
        ];

        yield [
            '<?php
                    if (
$a
                        && $b
) {
                    } elseif (
$c
                        && $d
) {
                    } else {
                    }',
            '<?php
                    if ($a
                        && $b) {
                    } elseif ($c
                        && $d) {
                    } else {
                    }',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_CONTROL_STRUCTURES]],
        ];

        yield [
            '<?php
                    while (
$a
                        && $b
) {
                    }',
            '<?php
                    while ($a
                        && $b) {
                    }',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_CONTROL_STRUCTURES]],
        ];

        yield [
            '<?php
                    try {}
                    catch (
\LogicException
                     | \RuntimeException $e
) {
                    };',
            '<?php
                    try {}
                    catch (\LogicException
                     | \RuntimeException $e) {
                    };',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_CONTROL_STRUCTURES]],
        ];

        yield [
            '<?php
                    $a = [
                        1,
"2
                        ",
3
                    ];',
            '<?php
                    $a = [
                        1, "2
                        ", 3
                    ];',
        ];

        yield [
            '<?php

            switch ($foo) {
                case 1:
case 2:
default:
            }
            ',
            '<?php

            switch ($foo) {
                case 1: case 2: default:
            }
            ',
            ['elements' => [SingleExpressionPerLineFixer::SWITCH_CASES]],
        ];

        yield [
            '<?php

            switch ($foo) {
                case 1:
                case 2:
                    $a = $b ? $c : $d;
            }
            ',
            null,
            ['elements' => [SingleExpressionPerLineFixer::SWITCH_CASES]],
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php
                    match (
$a
                        + $b
) {
                        1 => 1, 2 => 2,
                        3, 4 => 4,
                    };',
            '<?php
                    match ($a
                        + $b) {
                        1 => 1, 2 => 2,
                        3, 4 => 4,
                    };',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_CONTROL_STRUCTURES]],
        ];

        yield [
            '<?php
                    match ($a
                        + $b) {
                        1 => 1,
2 => 2,
                        3,
4 => 4,
                    };',
            '<?php
                    match ($a
                        + $b) {
                        1 => 1, 2 => 2,
                        3, 4 => 4,
                    };',
            ['elements' => [SingleExpressionPerLineFixer::MATCH_EXPRESSIONS]],
        ];

        yield [
            '<?php
                    match (
foo(
$a,
                        $b
)
) {
                        1 => 1, 2 => 2,
                        3, 4 => 4,
                    };',
            '<?php
                    match (foo($a,
                        $b)) {
                        1 => 1, 2 => 2,
                        3, 4 => 4,
                    };',
            ['elements' => [SingleExpressionPerLineFixer::ELEMENTS_ARGUMENTS, SingleExpressionPerLineFixer::ELEMENTS_CONTROL_STRUCTURES]],
        ];
    }
}
