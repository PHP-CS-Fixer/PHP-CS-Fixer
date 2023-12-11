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
 * @covers \PhpCsFixer\Fixer\ArrayNotation\AlphabeticalArrayKeySortFixer
 */
final class AlphabeticalArrayKeySortFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, string> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2?: array<string, string>}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'array with special case keys on bottom' => [
            '<?php [
                    "a.*" => "int",
                    "b" => "array",
                    "c.*" => "array",
                    "d" => "int",
                    "d.*" => "string",
                    "b" . "a" => "bla",
                    "a" . "a" => "bla",
                ];',
            '<?php [
                    "c.*" => "array",
                    "b" => "array",
                    "d.*" => "string",
                    "b" . "a" => "bla",
                    "a" . "a" => "bla",
                    "d" => "int",
                    "a.*" => "int",
                ];',
            [
                'sort_special_key_mode' => 'special_case_on_bottom',
            ],
        ];

        yield 'array with special case keys on top' => [
            '<?php [
                    "b" . "a" => "bla",
                    "a" . "a" => "bla",
                    "a.*" => "int",
                    "b" => "array",
                    "c.*" => "array",
                    "d" => "int",
                    "d.*" => "string",
                ];',
            '<?php [
                    "c.*" => "array",
                    "b" => "array",
                    "d.*" => "string",
                    "b" . "a" => "bla",
                    "a" . "a" => "bla",
                    "d" => "int",
                    "a.*" => "int",
                ];',
            [
                'sort_special_key_mode' => 'special_case_on_top',
            ],
        ];

        yield 'old-style array with string keys' => [
            '<?php array("a" => "a", "b" => "b", "c" => "c");',
            '<?php array("c" => "c", "a" => "a", "b" => "b");',
        ];

        yield 'old-style array with integer keys' => [
            '<?php array(1 => "one", 2 => "two", 3 => "three");',
            '<?php array(3 => "three", 1 => "one", 2 => "two");',
        ];

        yield 'old-style array with mixed keys' => [
            '<?php array("a" => "a", "b" => "b", 1 => "one", 2 => "two");',
            '<?php array("b" => "b", 1 => "one", 2 => "two", "a" => "a");',
        ];

        yield 'array with string keys' => [
            '<?php ["a" => "a", "b" => "b", "c" => "c"];',
            '<?php ["c" => "c", "a" => "a", "b" => "b"];',
        ];

        yield 'array with mixed keys, starting with integer' => [
            '<?php ["a" => "a", "b" => "b", "c" => "c", 0 => 0, 1 => 1, 2 => 2];',
            '<?php [2 => 2, "c" => "c", 0 => 0, "a" => "a", "b" => "b", 1 => 1];',
        ];

        yield 'array with integer keys' => [
            '<?php [1 => "one", 2 => "two", 3 => "three"];',
            '<?php [3 => "three", 1 => "one", 2 => "two"];',
        ];

        yield 'array with mixed keys, starting with string' => [
            '<?php ["a" => "a", "b" => "b", 1 => "one", 2 => "two"];',
            '<?php ["b" => "b", 1 => "one", 2 => "two", "a" => "a"];',
        ];

        yield 'function calls in keys, including first' => [
            '<?php ["a" => "value3", "b" => "value4", "c" => "value5", foo() . " baz" => "value1", bar() => "value2"];',
            '<?php [foo() . " baz" => "value1", "b" => "value4", bar() => "value2", "a" => "value3", "c" => "value5"];',
        ];

        yield 'function calls in keys, but first key as simple string' => [
            '<?php ["a" => "value3", "b" => "value4", kar() => "value4", foo() . " baz" => "value1", bar() => "value2"];',
            '<?php ["b" => "value4", kar() => "value4", foo() . " baz" => "value1", "a" => "value3", bar() => "value2"];',
        ];

        yield 'static call as one of values' => [
            '<?php array("a" => Arr::get($bla, "environment.bla"), "b" => "value3", "c" => "value1");',
            '<?php array("c" => "value1", "a" => Arr::get($bla, "environment.bla"), "b" => "value3");',
        ];

        yield 'multiline old-style array' => [
            '<?php array(
                        "a" => "value2",
                    "b" => "value3",
                    "c" => "value1"
                );',
            '<?php array(
                        "c" => "value1",
                    "a" => "value2",
                    "b" => "value3"
                );',
        ];

        yield 'multiline array' => [
            '<?php [
                        "a" => "value2",
                    "b" => "value3",
                    "c" => "value1"
                    ];',
            '<?php [
                        "c" => "value1",
                    "a" => "value2",
                    "b" => "value3"
                    ];',
        ];

        yield 'nested old-style arrays' => [
            '<?php array("a" => array("l" => "10", "m" => "20"), "b" => "2", "d" => "5");',
            '<?php array("b" => "2", "a" => array("m" => "20", "l" => "10"), "d" => "5");',
        ];

        yield 'nested arrays' => [
            '<?php ["a" => ["l" => "10", "m" => "20"], "b" => "2", "d" => "5"];',
            '<?php ["b" => "2", "a" => ["m" => "20", "l" => "10"], "d" => "5"];',
        ];

        yield 'mixed nested arrays' => [
            '<?php ["a" => array("l" => "10", "m" => "20"), "b" => "2", "d" => "5"];',
            '<?php ["b" => "2", "a" => array("m" => "20", "l" => "10"), "d" => "5"];',
        ];

        yield 'multiline mixed arrays' => [
            '<?php ["a" => array(
                        "l" => "10",
                        "m" => "20"),
                        "b" => "2",
                        "d" => "5"
                    ];',
            '<?php ["b" => "2",
                        "a" => array(
                        "m" => "20",
                        "l" => "10"),
                        "d" => "5"
                    ];',
        ];

        yield '3-level nesting' => [
            '<?php array("a" => array("l" => "10", "m" => "20", "z" => array("a" => "hame", "b" => "kame")), "b" => "2", "d" => "5");',
            '<?php array("b" => "2", "a" => array("m" => "20", "z" => array("b" => "kame", "a" => "hame"), "l" => "10"), "d" => "5");',
        ];

        yield 'array with implicit integer keys between string keys' => [
            '<?php [1, "test1" => 0,2,3, "test2" => 0];',
            '<?php [1, "test2" => 0,2,3, "test1" => 0];',
        ];

        yield 'array with implicit integer keys before string keys' => [
            '<?php [1,2,3, "test1" => 0, "test2" => 0];',
            '<?php [1,2,3, "test2" => 0, "test1" => 0];',
        ];

        yield 'collection-like same keys in multiple array items' => [
            '<?php [["test1" => 0, "test2" => 0],["test1" => 0, "test2" => 0]];',
            '<?php [["test2" => 0, "test1" => 0],["test2" => 0, "test1" => 0]];',
        ];

        yield 'complex nesting' => [
            '<?php [
                        "a" => [
                    "b" => [
                        "c" => sprintf("%s %s", Arr::get($data, "name"), Arr::get($data, "last_name")),
                        "d" => self::VARIABLE,
                    ],
                    "product" => [
                        "Colour" => self::VARIABLE,
                    ],
                ],
            ];',
            '<?php [
                        "a" => [
                    "b" => [
                        "d" => self::VARIABLE,
                        "c" => sprintf("%s %s", Arr::get($data, "name"), Arr::get($data, "last_name")),
                    ],
                    "product" => [
                        "Colour" => self::VARIABLE,
                    ],
                ],
            ];',
        ];

        yield 'comment between items' => [
            '<?php [
                    "a" => $date->copy()->addDays(60)->setTime(12, 0, 0)->toDateTimeString(),

                    // insert comment here.
                    "b" => self::VARIABLE,
                ];',
            '<?php [
                    "b" => self::VARIABLE,

                    // insert comment here.
                    "a" => $date->copy()->addDays(60)->setTime(12, 0, 0)->toDateTimeString(),
                ];',
        ];

        yield 'complex keys and values' => [
            '<?php [
                    "b" => 2,
                    things()() => Arr::get($something, $else) . Arr::get($different, $things),
                    Arr::get($something, $else) . Arr::get($different, $things) => 1,
                ];',
            '<?php [
                    things()() => Arr::get($something, $else) . Arr::get($different, $things),
                    "b" => 2,
                    Arr::get($something, $else) . Arr::get($different, $things) => 1,
                ];',
        ];
    }
}
