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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer
 */
final class FunctionsAnalyzerTest extends TestCase
{
    /**
     * @param bool   $isFunctionIndex
     * @param string $code
     * @param int    $index
     *
     * @dataProvider provideIsGlobalFunctionCallCases
     */
    public function testIsGlobalFunctionCall($isFunctionIndex, $code, $index)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        static::assertSame($isFunctionIndex, $analyzer->isGlobalFunctionCall($tokens, $index));
    }

    public function provideIsGlobalFunctionCallCases()
    {
        return [
            [
                false,
                '<?php CONSTANT;',
                1,
            ],
            [
                true,
                '<?php foo("bar");',
                1,
            ],
            [
                false,
                '<?php \foo("bar");',
                1,
            ],
            [
                true,
                '<?php \foo("bar");',
                2,
            ],
            [
                false,
                '<?php foo\bar("baz");',
                1,
            ],
            [
                false,
                '<?php foo\bar("baz");',
                3,
            ],
            [
                false,
                '<?php foo::bar("baz");',
                1,
            ],
            [
                false,
                '<?php foo::bar("baz");',
                3,
            ],
            [
                false,
                '<?php $foo->bar("baz");',
                3,
            ],
            [
                false,
                '<?php new bar("baz");',
                3,
            ],
            [
                false,
                '<?php function foo() {}',
                3,
            ],
            [
                false,
                '<?php function & foo() {}',
                5,
            ],
            [
                false,
                '<?php namespace\foo("bar");',
                3,
            ],
        ];
    }

    /**
     * @param bool   $isFunctionIndex
     * @param string $code
     * @param int    $index
     *
     * @dataProvider provideIsGlobalFunctionCallPhp74Cases
     * @requires PHP 7.4
     */
    public function testIsGlobalFunctionCallPhp74($isFunctionIndex, $code, $index)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        static::assertSame($isFunctionIndex, $analyzer->isGlobalFunctionCall($tokens, $index));
    }

    public function provideIsGlobalFunctionCallPhp74Cases()
    {
        return [
            [
                false,
                '<?php $foo = fn() => false;',
                5,
            ],
        ];
    }

    /**
     * @param string $code
     * @param int    $methodIndex
     * @param array  $expected
     *
     * @dataProvider provideFunctionsWithArgumentsCases
     */
    public function testFunctionArgumentInfo($code, $methodIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        static::assertSame(serialize($expected), serialize($analyzer->getFunctionArguments($tokens, $methodIndex)));
    }

    /**
     * @param string $code
     * @param int    $methodIndex
     * @param array  $expected
     *
     * @dataProvider provideFunctionsWithReturnTypeCases
     */
    public function testFunctionReturnTypeInfo($code, $methodIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        static::assertSame(serialize($expected), serialize($analyzer->getFunctionReturnType($tokens, $methodIndex)));
    }

    public function provideFunctionsWithArgumentsCases()
    {
        return [
            ['<?php function(){};', 1, []],
            ['<?php function($a){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null
                ),
            ]],
            ['<?php function($a, $b){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null
                ),
                '$b' => new ArgumentAnalysis(
                    '$b',
                    6,
                    null,
                    null
                ),
            ]],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null
                ),
                '$b' => new ArgumentAnalysis(
                    '$b',
                    6,
                    'array(1,2)',
                    null
                ),
                '$c' => new ArgumentAnalysis(
                    '$c',
                    18,
                    '3',
                    null
                ),
            ]],
            ['<?php function(array $a = array()){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    5,
                    'array()',
                    new TypeAnalysis(
                        'array',
                        3,
                        3
                    )
                ),
            ]],
            ['<?php function(array ... $a){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    7,
                    null,
                    new TypeAnalysis(
                        'array',
                        3,
                        3
                    )
                ),
            ]],
            ['<?php function(\Foo\Bar $a){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    8,
                    null,
                    new TypeAnalysis(
                        '\Foo\Bar',
                        3,
                        6
                    )
                ),
            ]],
            ['<?php function(\Foo/** TODO: change to something else */\Bar $a){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    9,
                    null,
                    new TypeAnalysis(
                        '\Foo\Bar',
                        3,
                        7
                    )
                ),
            ]],
        ];
    }

    public function provideFunctionsWithReturnTypeCases()
    {
        return [
            ['<?php function(){};', 1, null],
            ['<?php function($a): array {};', 1, new TypeAnalysis('array', 7, 7)],
            ['<?php function($a): \Foo\Bar {};', 1, new TypeAnalysis('\Foo\Bar', 7, 10)],
            ['<?php function($a): /* not sure if really an array */array {};', 1, new TypeAnalysis('array', 8, 8)],
            ['<?php function($a): \Foo/** TODO: change to something else */\Bar {};', 1, new TypeAnalysis('\Foo\Bar', 7, 11)],
        ];
    }

    /**
     * @param string $code
     * @param int    $methodIndex
     * @param array  $expected
     *
     * @dataProvider provideFunctionsWithArgumentsPhp74Cases
     * @requires PHP 7.4
     */
    public function testFunctionArgumentInfoPhp74($code, $methodIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        static::assertSame(serialize($expected), serialize($analyzer->getFunctionArguments($tokens, $methodIndex)));
    }

    public function provideFunctionsWithArgumentsPhp74Cases()
    {
        return [
            ['<?php fn() => null;', 1, []],
            ['<?php fn($a) => null;', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null
                ),
            ]],
            ['<?php fn($a, $b) => null;', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null
                ),
                '$b' => new ArgumentAnalysis(
                    '$b',
                    6,
                    null,
                    null
                ),
            ]],
            ['<?php fn($a, $b = array(1,2), $c = 3) => null;', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null
                ),
                '$b' => new ArgumentAnalysis(
                    '$b',
                    6,
                    'array(1,2)',
                    null
                ),
                '$c' => new ArgumentAnalysis(
                    '$c',
                    18,
                    '3',
                    null
                ),
            ]],
            ['<?php fn(array $a = array()) => null;', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    5,
                    'array()',
                    new TypeAnalysis(
                        'array',
                        3,
                        3
                    )
                ),
            ]],
            ['<?php fn(array ... $a) => null;', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    7,
                    null,
                    new TypeAnalysis(
                        'array',
                        3,
                        3
                    )
                ),
            ]],
            ['<?php fn(\Foo\Bar $a) => null;', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    8,
                    null,
                    new TypeAnalysis(
                        '\Foo\Bar',
                        3,
                        6
                    )
                ),
            ]],
            ['<?php fn(\Foo/** TODO: change to something else */\Bar $a) => null;', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    9,
                    null,
                    new TypeAnalysis(
                        '\Foo\Bar',
                        3,
                        7
                    )
                ),
            ]],
        ];
    }

    /**
     * @param string $code
     * @param int    $methodIndex
     * @param array  $expected
     *
     * @dataProvider provideFunctionsWithReturnTypePhp74Cases
     * @requires PHP 7.4
     */
    public function testFunctionReturnTypeInfoPhp74($code, $methodIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        $actual = $analyzer->getFunctionReturnType($tokens, $methodIndex);
        static::assertSame(serialize($expected), serialize($actual));
    }

    public function provideFunctionsWithReturnTypePhp74Cases()
    {
        return [
            ['<?php fn() => null;', 1, null],
            ['<?php fn($a): array => null;', 1, new TypeAnalysis('array', 7, 7)],
            ['<?php fn($a): \Foo\Bar => null;', 1, new TypeAnalysis('\Foo\Bar', 7, 10)],
            ['<?php fn($a): /* not sure if really an array */array => null;', 1, new TypeAnalysis('array', 8, 8)],
            ['<?php fn($a): \Foo/** TODO: change to something else */\Bar => null;', 1, new TypeAnalysis('\Foo\Bar', 7, 11)],
        ];
    }

    /**
     * @param bool   $isTheSameClassCall
     * @param string $code
     * @param int    $index
     *
     * @dataProvider provideIsTheSameClassCallCases
     */
    public function testIsTheSameClassCall($isTheSameClassCall, $code, $index)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        static::assertSame($isTheSameClassCall, $analyzer->isTheSameClassCall($tokens, $index));
    }

    public function provideIsTheSameClassCallCases()
    {
        $template = '<?php
            class Foo {
                public function methodOne() {
                    $x = %sotherMethod(1, 2, 3);
                }
            }
        ';

        yield [
            false,
            sprintf($template, '$this->'),
            -1,
        ];

        // 24 is index of "otherMethod" token

        for ($i = 0; $i < 40; ++$i) {
            yield [
                24 === $i,
                sprintf($template, '$this->'),
                $i,
            ];
            yield [
                24 === $i,
                sprintf($template, 'self::'),
                $i,
            ];
            yield [
                24 === $i,
                sprintf($template, 'static::'),
                $i,
            ];
        }

        yield [
            true,
            sprintf($template, '$THIS->'),
            24,
        ];

        yield [
            false,
            sprintf($template, '$notThis->'),
            24,
        ];

        yield [
            false,
            sprintf($template, 'Bar::'),
            24,
        ];
    }
}
