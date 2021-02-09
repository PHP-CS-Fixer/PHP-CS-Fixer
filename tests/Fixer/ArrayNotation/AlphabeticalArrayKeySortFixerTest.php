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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        if (0 !== \count($config)) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
                '<?php array("a" => "value2", "b" => "value3", "c" => "value1");',
                '<?php array("c" => "value1", "a" => "value2", "b" => "value3");',
            ],
            [
                '<?php ["a" => "value2", "b" => "value3", "c" => "value1"];',
                '<?php ["c" => "value1", "a" => "value2", "b" => "value3"];',
            ],
            [
                '<?php ["a" => "value3", "b" => "value4", "c" => "value5", foo() . " baz" => "value1", bar() => "value2"];',
                '<?php [foo() . " baz" => "value1", "b" => "value4", bar() => "value2", "a" => "value3", "c" => "value5"];',
            ],
            [
                '<?php ["a" => "value3", "b" => "value4", kar() => "value4", foo() . " baz" => "value1", bar() => "value2"];',
                '<?php ["b" => "value4", kar() => "value4", foo() . " baz" => "value1", "a" => "value3", bar() => "value2"];',
            ],

            [
                '<?php array("a" => "hey how, am i doing", "b" => "value3", "c" => "value1");',
                '<?php array("c" => "value1", "a" => "hey how, am i doing", "b" => "value3");',
            ],
            [
                '<?php array("a" => Arr::get($bla, "environment.bla"), "b" => "value3", "c" => "value1");',
                '<?php array("c" => "value1", "a" => Arr::get($bla, "environment.bla"), "b" => "value3");',
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php array("a" => array("l" => "10", "m" => "20"), "b" => "2", "d" => "5");',
                '<?php array("b" => "2", "a" => array("m" => "20", "l" => "10"), "d" => "5");',
            ],

            [
                '<?php ["a" => ["l" => "10", "m" => "20"], "b" => "2", "d" => "5"];',
                '<?php ["b" => "2", "a" => ["m" => "20", "l" => "10"], "d" => "5"];',
            ],
            [
                '<?php ["a" => array("l" => "10", "m" => "20"), "b" => "2", "d" => "5"];',
                '<?php ["b" => "2", "a" => array("m" => "20", "l" => "10"), "d" => "5"];',
            ],
            [
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
            ],
            [
                '<?php array("a" => array("l" => "10", "m" => "20", "z" => array("a" => "hame", "b" => "kame")), "b" => "2", "d" => "5");',
                '<?php array("b" => "2", "a" => array("m" => "20", "z" => array("b" => "kame", "a" => "hame"), "l" => "10"), "d" => "5");',
            ],
            [
                '<?php [1, "test1" => 0,2,3, "test2" => 0];',
                '<?php [1, "test2" => 0,2,3, "test1" => 0];',
            ],
            [
                '<?php [1,2,3, "test1" => 0, "test2" => 0];',
                '<?php [1,2,3, "test2" => 0, "test1" => 0];',
            ],
            [
                '<?php [["test1" => 0, "test2" => 0],["test1" => 0, "test2" => 0]];',
                '<?php [["test2" => 0, "test1" => 0],["test2" => 0, "test1" => 0]];',
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixPhp70Cases
     * @requires PHP 7.0
     */
    public function testFixPhp70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPhp70Cases()
    {
        return [
             [
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
            ],
        ];
    }
}
