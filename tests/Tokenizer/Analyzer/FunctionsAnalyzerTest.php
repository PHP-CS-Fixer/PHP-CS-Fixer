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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FunctionsAnalyzerTest extends TestCase
{
    /**
     * @param list<int> $expectedIndices
     *
     * @dataProvider provideIsGlobalFunctionCallCases
     */
    public function testIsGlobalFunctionCall(string $code, array $expectedIndices): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        $calculatedIndices = [];
        foreach ($tokens as $index => $token) {
            if ($analyzer->isGlobalFunctionCall($tokens, $index)) {
                $calculatedIndices[] = $index;
            }
        }

        self::assertSame(
            $expectedIndices,
            $calculatedIndices,
            \sprintf(
                'Global function calls found at positions: [%s], expected at [%s].',
                implode(', ', $calculatedIndices),
                implode(', ', $expectedIndices)
            )
        );
    }

    /**
     * @return iterable<array{string, list<int>}>
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
            '<?php
                namespace A {
                    use function A;
                }
                namespace B {
                    use function D;
                    A();
                }
            ',
            [30],
        ];

        yield [
            '<?php
                function A(){}
                A();
            ',
            [10],
        ];

        yield [
            '<?php
                function A(){}
                a();
            ',
            [10],
        ];

        yield [
            '<?php
                namespace {
                    function A(){}
                    A();
                }
            ',
            [14],
        ];

        yield [
            '<?php
                namespace Z {
                    function A(){}
                    A();
                }
            ',
            [],
        ];

        yield [
            '<?php
            namespace Z;

            function A(){}
            A();
            ',
            [],
        ];

        yield 'function signature ref. return, calls itself' => [
            '<?php
                function & A(){}
                A();
            ',
            [12],
        ];

        yield [
            '<?php
                class Foo
                {
                    public function A(){}
                }
                A();
            ',
            [20],
        ];

        yield [
            '<?php
                namespace A {
                    function A(){}
                }
                namespace B {
                    A();
                }
            ',
            [24],
        ];

        yield [
            '<?php
                use function X\a;
                A();
            ',
            [],
        ];

        yield [
            '<?php
                use A;
                A();
            ',
            [7],
        ];

        yield [
            '<?php
                use const A;
                A();
            ',
            [9],
        ];

        yield [
            '<?php
                use function A;
                str_repeat($a, $b);
            ',
            [9],
        ];

        yield [
            '<?php
                namespace {
                    function A(){}
                    A();
                    $b = function(){};
                }
            ',
            [14],
        ];

        yield [
            '<?php implode($a);implode($a);implode($a);implode($a);implode($a);implode($a);',
            [1, 6, 11, 16, 21, 26],
        ];

        yield [
            '<?php
$z = new class(
    new class(){ private function A(){} }
){
    public function A() {}
};

A();
                ',
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

        yield 'functions that can be confused with a property hook' => [
            <<<'PHP'
                <?php
                $array = [
                    1 => 2,
                    set() => 3,
                    $foo . set() => 3,
                ];
                $x = set(set(), set());
                set();
                if (true) { set(); }
                set();
                PHP,
            [14, 27, 43, 45, 50, 56, 69, 76],
        ];
    }

    /**
     * @param list<int> $indices
     *
     * @dataProvider provideIsGlobalFunctionCallPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testIsGlobalFunctionCallPre80(string $code, array $indices): void
    {
        $this->testIsGlobalFunctionCall($code, $indices);
    }

    /**
     * @return iterable<int, array{string, list<int>}>
     */
    public static function provideIsGlobalFunctionCallPre80Cases(): iterable
    {
        yield [
            '<?php
                    use function \  str_repeat;
                    str_repeat($a, $b);
                ',
            [11],
        ];
    }

    /**
     * @param list<int> $indices
     *
     * @dataProvider provideIsGlobalFunctionCallPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsGlobalFunctionCallPhp80(string $code, array $indices): void
    {
        $this->testIsGlobalFunctionCall($code, $indices);
    }

    /**
     * @return iterable<int, array{string, list<int>}>
     */
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
            '<?php
#[\Attribute(\Attribute::TARGET_CLASS)]
class Foo {}
',
            [],
        ];

        yield [
            '<?php $x?->count();',
            [],
        ];

        yield [
            '<?php
                #[Foo(), Bar(), Baz()]
                class Foo {}
            ',
            [],
        ];
    }

    /**
     * @param list<int> $indices
     *
     * @dataProvider provideIsGlobalFunctionCallPhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testIsGlobalFunctionCallPhp81(string $code, array $indices): void
    {
        $this->testIsGlobalFunctionCall($code, $indices);
    }

    /**
     * @return iterable<array{string, list<int>}>
     */
    public static function provideIsGlobalFunctionCallPhp81Cases(): iterable
    {
        yield 'first class callable cases' => [
            '<?php
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
\'strlen\'(...);
[$obj, \'method\'](...);
[Foo::class, \'method\'](...);
$c = new class{};
$b = new class(){};
$a = new #[foo]
class(){};
',
            [],
        ];

        yield [
            '<?php foo("bar"); enum A { function Foo(){ foo(); } }',
            [1, 20],
        ];
    }

    /**
     * @param list<int> $indices
     *
     * @dataProvider provideIsGlobalFunctionCallPhp84Cases
     *
     * @requires PHP 8.4
     */
    public function testIsGlobalFunctionCallPhp84(string $code, array $indices): void
    {
        $this->testIsGlobalFunctionCall($code, $indices);
    }

    /**
     * @return iterable<string, array{string, list<int>}>
     */
    public static function provideIsGlobalFunctionCallPhp84Cases(): iterable
    {
        yield 'property hooks' => [
            <<<'PHP'
                <?php
                class GetFirst
                {
                    public string $bothWithDoubleArrow = '' {
                        get => '';
                        set(string $x) => $x;
                    }
                    public string $getWithDoubleArrow = '' {
                        get => '';
                        set(string $x) { ''; }
                    }
                    public string $setWithDoubleArrow = '' {
                        get { ''; }
                        set(string $x) => $x;
                    }
                    public string $bothWithBraces = '' {
                        get { ''; }
                        set(string $x) { ''; }
                    }
                    public string $setUppercase = '' {
                        get { ''; }
                        SET(string $x) { ''; }
                    }
                }
                class SetFirst
                {
                    public string $bothWithDoubleArrow = '' {
                        set(string $x) => $x;
                        get => '';
                    }
                    public string $getWithDoubleArrow = '' {
                        set(string $x) { ''; }
                        get => '';
                    }
                    public string $setWithDoubleArrow = '' {
                        set(string $x) => $x;
                        get { ''; }
                    }
                    public string $bothWithBraces = '' {
                        set(string $x) { ''; }
                        get { ''; }
                    }
                    public string $setUppercase = '' {
                        SET(string $x) { ''; }
                        get { ''; }
                    }
                }
                PHP,
            [],
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
     * @return iterable<int, array{string, int, array<string, ArgumentAnalysis>}>
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
        $this->testFunctionArgumentInfo($code, $methodIndex, $expected);
    }

    /**
     * @return iterable<int, array{string, int, array<string, ArgumentAnalysis>}>
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
     * @return iterable<int, array{string, int, null|TypeAnalysis}>
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
        $this->testFunctionReturnTypeInfo($code, $methodIndex, $expected);
    }

    /**
     * @return iterable<int, array{string, int, null|TypeAnalysis}>
     */
    public static function provideFunctionReturnTypeInfoPre80Cases(): iterable
    {
        yield ['<?php function($a): \Foo/** TODO: change to something else */\Bar {};', 1, new TypeAnalysis('\Foo\Bar', 7, 11)];

        yield ['<?php fn($a): \Foo/** TODO: change to something else */\Bar => null;', 1, new TypeAnalysis('\Foo\Bar', 7, 11)];
    }

    public function testIsTheSameClassCallInvalidIndex(): void
    {
        $tokens = Tokens::fromCode('<?php 1;2;');
        $analyzer = new FunctionsAnalyzer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Token index 666 does not exist.');

        $analyzer->isTheSameClassCall($tokens, 666);
    }

    /**
     * @dataProvider provideIsTheSameClassCallCases
     *
     * @param list<int> $sameClassCallIndices
     */
    public function testIsTheSameClassCall(string $code, array $sameClassCallIndices): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            self::assertSame(
                \in_array($index, $sameClassCallIndices, true),
                $analyzer->isTheSameClassCall($tokens, $index),
                \sprintf('Index %d failed check.', $index)
            );
        }
    }

    /**
     * @return iterable<int, array{string, list<int>}>
     */
    public static function provideIsTheSameClassCallCases(): iterable
    {
        $template = '<?php
            class Foo {
                public function methodOne() {
                    $x = %sotherMethod(1, 2, 3);
                }
            }
        ';

        yield [
            \sprintf($template, '$this->'),
            [24],
        ];

        yield [
            \sprintf($template, 'self::'),
            [24],
        ];

        yield [
            \sprintf($template, 'static::'),
            [24],
        ];

        yield [
            \sprintf($template, '$THIS->'),
            [24],
        ];

        yield [
            \sprintf($template, '$notThis->'),
            [],
        ];

        yield [
            \sprintf($template, 'Bar::'),
            [],
        ];

        yield [
            \sprintf($template, '$this::'),
            [24],
        ];

        yield [
            <<<'PHP'
                <?php
                class Foo {
                    private $bar;
                    public function bar() {
                        return $this->bar;
                    }
                }
                PHP,
            [],
        ];
    }

    /**
     * @dataProvider provideIsTheSameClassCall80Cases
     *
     * @param list<int> $sameClassCallIndices
     *
     * @requires PHP 8.0
     */
    public function testIsTheSameClassCall80(string $code, array $sameClassCallIndices): void
    {
        $this->testIsTheSameClassCall($code, $sameClassCallIndices);
    }

    /**
     * @return iterable<int, array{string, list<int>}>
     */
    public static function provideIsTheSameClassCall80Cases(): iterable
    {
        yield [
            '<?php
                class Foo {
                    public function methodOne() {
                        $x = $this?->otherMethod(1, 2, 3);
                    }
                }
            ',
            [24],
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
        $this->testFunctionArgumentInfo($code, $methodIndex, $expected);
    }

    /**
     * @return iterable<int, array{string, int, array<string, ArgumentAnalysis>}>
     */
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
     * @param array<string, ArgumentAnalysis> $expected
     *
     * @dataProvider provideFunctionArgumentInfoPhp84Cases
     *
     * @requires PHP 8.4
     */
    public function testFunctionArgumentInfoPhp84(string $code, int $methodIndex, array $expected): void
    {
        $this->testFunctionArgumentInfo($code, $methodIndex, $expected);
    }

    /**
     * @return iterable<string, array{string, int, array<string, ArgumentAnalysis>}>
     */
    public static function provideFunctionArgumentInfoPhp84Cases(): iterable
    {
        yield 'asymmetric visibility' => [
            <<<'PHP'
                <?php
                class Foo {
                    public function __construct(
                        public public(set) bool $b,
                        public protected(set) int|null $i,
                        protected private(set) ?string $s,
                    ) {}
                }
                PHP,
            9,
            [
                '$b' => new ArgumentAnalysis(
                    '$b',
                    20,
                    null,
                    new TypeAnalysis(
                        'bool',
                        18,
                        18,
                    ),
                ),
                '$i' => new ArgumentAnalysis(
                    '$i',
                    31,
                    null,
                    new TypeAnalysis(
                        'int|null',
                        27,
                        29,
                    ),
                ),
                '$s' => new ArgumentAnalysis(
                    '$s',
                    41,
                    null,
                    new TypeAnalysis(
                        '?string',
                        38,
                        39,
                    ),
                ),
            ],
        ];
    }
}
