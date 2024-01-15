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
     * @param int[] $indices
     *
     * @dataProvider provideIsGlobalFunctionCallCases
     */
    public function testIsGlobalFunctionCall(string $code, array $indices): void
    {
        self::assertIsGlobalFunctionCall($indices, $code);
    }

    /**
     * @return iterable<array{string, array<int>}>
     */
    public static function provideIsGlobalFunctionCallCases(): iterable
    {
        yield [
            '<?php CONSTANT;',
            [],
        ];

        yield [
            '<?php foo();',
            [1],
        ];

        yield [
            '<?php foo("bar");',
            [1],
        ];

        yield [
            '<?php \foo("bar");',
            [2],
        ];

        yield [
            '<?php foo\bar("baz");',
            [],
        ];

        yield [
            '<?php foo\bar("baz");',
            [],
        ];

        yield [
            '<?php foo::bar("baz");',
            [],
        ];

        yield [
            '<?php foo::bar("baz");',
            [],
        ];

        yield [
            '<?php $foo->bar("baz");',
            [],
        ];

        yield [
            '<?php new bar("baz");',
            [],
        ];

        yield [
            '<?php function foo() {}',
            [],
        ];

        yield 'function with ref. return' => [
            '<?php function & foo() {}',
            [],
        ];

        yield [
            '<?php namespace\foo("bar");',
            [],
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace A {
                                    use function A;
                                }
                                namespace B {
                                    use function D;
                                    A();
                                }
                EOD."\n            ",
            [30],
        ];

        yield [
            <<<'EOD'
                <?php
                                function A(){}
                                A();
                EOD."\n            ",
            [10],
        ];

        yield [
            <<<'EOD'
                <?php
                                function A(){}
                                a();
                EOD."\n            ",
            [10],
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace {
                                    function A(){}
                                    A();
                                }
                EOD."\n            ",
            [14],
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace Z {
                                    function A(){}
                                    A();
                                }
                EOD."\n            ",
            [],
        ];

        yield [
            <<<'EOD'
                <?php
                            namespace Z;

                            function A(){}
                            A();
                EOD."\n            ",
            [],
        ];

        yield 'function signature ref. return, calls itself' => [
            <<<'EOD'
                <?php
                                function & A(){}
                                A();
                EOD."\n            ",
            [12],
        ];

        yield [
            <<<'EOD'
                <?php
                                class Foo
                                {
                                    public function A(){}
                                }
                                A();
                EOD."\n            ",
            [20],
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace A {
                                    function A(){}
                                }
                                namespace B {
                                    A();
                                }
                EOD."\n            ",
            [24],
        ];

        yield [
            <<<'EOD'
                <?php
                                use function X\a;
                                A();
                EOD."\n            ",
            [],
        ];

        yield [
            <<<'EOD'
                <?php
                                use A;
                                A();
                EOD."\n            ",
            [7],
        ];

        yield [
            <<<'EOD'
                <?php
                                use const A;
                                A();
                EOD."\n            ",
            [9],
        ];

        yield [
            <<<'EOD'
                <?php
                                use function A;
                                str_repeat($a, $b);
                EOD."\n            ",
            [9],
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace {
                                    function A(){}
                                    A();
                                    $b = function(){};
                                }
                EOD."\n            ",
            [14],
        ];

        yield [
            '<?php implode($a);implode($a);implode($a);implode($a);implode($a);implode($a);',
            [1, 6, 11, 16, 21, 26],
        ];

        yield [
            <<<'EOD'
                <?php
                $z = new class(
                    new class(){ private function A(){} }
                ){
                    public function A() {}
                };

                A();
                EOD."\n                ",
            [46],
        ];

        yield [
            '<?php $foo = fn() => false;',
            [],
        ];

        yield [
            '<?php foo("bar"); class A { function Foo(){ foo(); } }',
            [1, 20],
        ];
    }

    /**
     * @param int[] $indices
     *
     * @dataProvider provideIsGlobalFunctionCallPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testIsGlobalFunctionCallPre80(string $code, array $indices): void
    {
        self::assertIsGlobalFunctionCall($indices, $code);
    }

    /**
     * @return iterable<array{string, array<int>}>
     */
    public static function provideIsGlobalFunctionCallPre80Cases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                    use function \  str_repeat;
                                    str_repeat($a, $b);
                EOD."\n                ",
            [11],
        ];
    }

    /**
     * @param int[] $indices
     *
     * @dataProvider provideIsGlobalFunctionCallPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsGlobalFunctionCallPhp80(string $code, array $indices): void
    {
        self::assertIsGlobalFunctionCall($indices, $code);
    }

    public static function provideIsGlobalFunctionCallPhp80Cases(): iterable
    {
        yield [
            '<?php $a = new (foo());',
            [8],
        ];

        yield [
            '<?php $b = $foo instanceof (foo());',
            [10],
        ];

        yield [
            <<<'EOD'
                <?php
                #[\Attribute(\Attribute::TARGET_CLASS)]
                class Foo {}

                EOD,
            [],
        ];

        yield [
            '<?php $x?->count();',
            [],
        ];

        yield [
            <<<'EOD'
                <?php
                                #[Foo(), Bar(), Baz()]
                                class Foo {}
                EOD."\n            ",
            [],
        ];
    }

    /**
     * @param int[] $indices
     *
     * @dataProvider provideIsGlobalFunctionCallPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testIsGlobalFunctionCallPhp81(array $indices, string $code): void
    {
        self::assertIsGlobalFunctionCall($indices, $code);
    }

    public static function provideIsGlobalFunctionCallPhp81Cases(): iterable
    {
        yield 'first class callable cases' => [
            [],
            <<<'EOD'
                <?php
                strlen(...);
                \strlen(...);
                $closure(...);
                $invokableObject(...);
                $obj->method(...);
                $obj->$methodStr(...);
                ($obj->property)(...);
                Foo::method(...);
                $classStr::$methodStr(...);
                self::{$complex . $expression}(...);
                'strlen'(...);
                [$obj, 'method'](...);
                [Foo::class, 'method'](...);
                $c = new class{};
                $b = new class(){};
                $a = new #[foo]
                class(){};

                EOD,
        ];

        yield [
            [1, 20],
            '<?php foo("bar"); enum A { function Foo(){ foo(); } }',
        ];
    }

    /**
     * @param array<string, ArgumentAnalysis> $expected
     *
     * @dataProvider provideFunctionArgumentInfoCases
     */
    public function testFunctionArgumentInfo(string $code, int $methodIndex, array $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        self::assertSame(serialize($expected), serialize($analyzer->getFunctionArguments($tokens, $methodIndex)));
    }

    /**
     * @return iterable<array{string, int, array<ArgumentAnalysis>}>
     */
    public static function provideFunctionArgumentInfoCases(): iterable
    {
        yield ['<?php function(){};', 1, []];

        yield ['<?php function($a){};', 1, [
            '$a' => new ArgumentAnalysis(
                '$a',
                3,
                null,
                null
            ),
        ]];

        yield ['<?php function($a, $b){};', 1, [
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
        ]];

        yield ['<?php function($a, $b = array(1,2), $c = 3){};', 1, [
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
        ]];

        yield ['<?php function(array $a = array()){};', 1, [
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
        ]];

        yield ['<?php function(array ... $a){};', 1, [
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
        ]];

        yield ['<?php function(\Foo\Bar $a){};', 1, [
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
        ]];

        yield ['<?php fn() => null;', 1, []];

        yield ['<?php fn($a) => null;', 1, [
            '$a' => new ArgumentAnalysis(
                '$a',
                3,
                null,
                null
            ),
        ]];

        yield ['<?php fn($a, $b) => null;', 1, [
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
        ]];

        yield ['<?php fn($a, $b = array(1,2), $c = 3) => null;', 1, [
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
        ]];

        yield ['<?php fn(array $a = array()) => null;', 1, [
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
        ]];

        yield ['<?php fn(array ... $a) => null;', 1, [
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
        ]];

        yield ['<?php fn(\Foo\Bar $a) => null;', 1, [
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
        ]];
    }

    /**
     * @param array<string, ArgumentAnalysis> $expected
     *
     * @dataProvider provideFunctionArgumentInfoPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFunctionArgumentInfoPre80(string $code, int $methodIndex, array $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        self::assertSame(serialize($expected), serialize($analyzer->getFunctionArguments($tokens, $methodIndex)));
    }

    /**
     * @return iterable<array{string, int, array<ArgumentAnalysis>}>
     */
    public static function provideFunctionArgumentInfoPre80Cases(): iterable
    {
        yield ['<?php fn(\Foo/** TODO: change to something else */\Bar $a) => null;', 1, [
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
        ]];

        yield ['<?php function(\Foo/** TODO: change to something else */\Bar $a){};', 1, [
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
        ]];
    }

    /**
     * @dataProvider provideFunctionReturnTypeInfoCases
     */
    public function testFunctionReturnTypeInfo(string $code, int $methodIndex, ?TypeAnalysis $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();
        $actual = $analyzer->getFunctionReturnType($tokens, $methodIndex);

        self::assertSame(serialize($expected), serialize($actual));
    }

    /**
     * @return iterable<array{string, int, null|TypeAnalysis}>
     */
    public static function provideFunctionReturnTypeInfoCases(): iterable
    {
        yield ['<?php function(){};', 1, null];

        yield ['<?php function($a): array {};', 1, new TypeAnalysis('array', 7, 7)];

        yield ['<?php function($a): \Foo\Bar {};', 1, new TypeAnalysis('\Foo\Bar', 7, 10)];

        yield ['<?php function($a): /* not sure if really an array */array {};', 1, new TypeAnalysis('array', 8, 8)];

        yield ['<?php fn() => null;', 1, null];

        yield ['<?php fn(array $a) => null;', 1, null];

        yield ['<?php fn($a): array => null;', 1, new TypeAnalysis('array', 7, 7)];

        yield ['<?php fn($a): \Foo\Bar => null;', 1, new TypeAnalysis('\Foo\Bar', 7, 10)];

        yield ['<?php fn($a): /* not sure if really an array */array => null;', 1, new TypeAnalysis('array', 8, 8)];
    }

    /**
     * @dataProvider provideFunctionReturnTypeInfoPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFunctionReturnTypeInfoPre80(string $code, int $methodIndex, ?TypeAnalysis $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();
        $actual = $analyzer->getFunctionReturnType($tokens, $methodIndex);

        self::assertSame(serialize($expected), serialize($actual));
    }

    /**
     * @return iterable<array{string, int, null|TypeAnalysis}>
     */
    public static function provideFunctionReturnTypeInfoPre80Cases(): iterable
    {
        yield ['<?php function($a): \Foo/** TODO: change to something else */\Bar {};', 1, new TypeAnalysis('\Foo\Bar', 7, 11)];

        yield ['<?php fn($a): \Foo/** TODO: change to something else */\Bar => null;', 1, new TypeAnalysis('\Foo\Bar', 7, 11)];
    }

    /**
     * @dataProvider provideIsTheSameClassCallCases
     */
    public function testIsTheSameClassCall(bool $isTheSameClassCall, string $code, int $index): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        self::assertSame($isTheSameClassCall, $analyzer->isTheSameClassCall($tokens, $index));
    }

    /**
     * @return iterable<array{bool, string, int}>
     */
    public static function provideIsTheSameClassCallCases(): iterable
    {
        $template = <<<'EOD'
            <?php
                        class Foo {
                            public function methodOne() {
                                $x = %sotherMethod(1, 2, 3);
                            }
                        }
            EOD."\n        ";

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

        yield [
            true,
            sprintf($template, '$this::'),
            24,
        ];
    }

    /**
     * @dataProvider provideIsTheSameClassCall80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsTheSameClassCall80(bool $isTheSameClassCall, string $code, int $index): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        self::assertSame($isTheSameClassCall, $analyzer->isTheSameClassCall($tokens, $index));
    }

    /**
     * @return iterable<array{bool, string, int}>
     */
    public static function provideIsTheSameClassCall80Cases(): iterable
    {
        yield [
            true,
            <<<'EOD'
                <?php
                                class Foo {
                                    public function methodOne() {
                                        $x = $this?->otherMethod(1, 2, 3);
                                    }
                                }
                EOD."\n            ",
            24,
        ];
    }

    /**
     * @param array<string, ArgumentAnalysis> $expected
     *
     * @dataProvider provideFunctionArgumentInfoPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFunctionArgumentInfoPhp80(string $code, int $methodIndex, array $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        self::assertSame(serialize($expected), serialize($analyzer->getFunctionArguments($tokens, $methodIndex)));
    }

    public static function provideFunctionArgumentInfoPhp80Cases(): iterable
    {
        yield ['<?php function($aa,){};', 1, [
            '$aa' => new ArgumentAnalysis(
                '$aa',
                3,
                null,
                null
            ),
        ]];

        yield ['<?php fn($a,    $bc  ,) => null;', 1, [
            '$a' => new ArgumentAnalysis(
                '$a',
                3,
                null,
                null
            ),
            '$bc' => new ArgumentAnalysis(
                '$bc',
                6,
                null,
                null
            ),
        ]];
    }

    /**
     * @param int[] $expectedIndices
     */
    private static function assertIsGlobalFunctionCall(array $expectedIndices, string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();
        $actualIndices = [];

        foreach ($tokens as $index => $token) {
            if ($analyzer->isGlobalFunctionCall($tokens, $index)) {
                $actualIndices[] = $index;
            }
        }

        self::assertSame(
            $expectedIndices,
            $actualIndices,
            sprintf(
                'Global function calls found at positions: [%s], expected at [%s].',
                implode(', ', $actualIndices),
                implode(', ', $expectedIndices)
            )
        );
    }
}
