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
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ArgumentsAnalyzerTest extends TestCase
{
    /**
     * @param array<int, int> $arguments
     *
     * @dataProvider provideArgumentsCases
     */
    public function testArguments(string $code, int $openIndex, int $closeIndex, array $arguments): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        self::assertSame(\count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        self::assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

    /**
     * @return iterable<array{string, int, int, array<int, int>}>
     */
    public static function provideArgumentsCases(): iterable
    {
        yield ['<?php function(){};', 2, 3, []];

        yield ['<?php foo();', 2, 3, []];

        yield ['<?php function($a){};', 2, 4, [3 => 3]];

        yield ['<?php \foo($a);', 3, 5, [4 => 4]];

        yield ['<?php function($a, $b){};', 2, 7, [3 => 3, 5 => 6]];

        yield ['<?php function($a, $b = array(1,2), $c = 3){};', 2, 23, [3 => 3, 5 => 15, 17 => 22]];

        yield 'non condition (hardcoded)' => [
            '<?php $x = strpos("foo", 123);',
            6,
            11,
            [7 => 7, 9 => 10],
        ];

        yield ['<?php \test($a+$b++, !$c);', 3, 12, [4 => 7, 9 => 11]];

        yield ['<?php $a = function(array &$a = array()){};', 6, 17, [7 => 16]];

        yield ['<?php $a = function( ... $z){};', 6, 11, [7 => 10]];

        yield ['<?php $a = function(array ... $a){};', 6, 12, [7 => 11]];

        yield ['<?php $a = function(\Foo\Bar $a, \Foo\Bar $b){};', 6, 21, [7 => 12, 14 => 20]];

        yield ['<?php foo($a,);', 2, 5, [3 => 3]];

        yield ['<?php foo($a,/**/);', 2, 6, [3 => 3]];

        yield ['<?php foo($a(1,2,3,4,5),);', 2, 16, [3 => 14]];

        yield ['<?php foo($a(1,2,3,4,5,),);', 2, 17, [3 => 15]];

        yield ['<?php foo($a(1,2,3,4,5,),);', 4, 15, [5 => 5, 7 => 7, 9 => 9, 11 => 11, 13 => 13]];

        yield ['<?php bar($a, $b , ) ;', 2, 10, [3 => 3, 5 => 7]];
    }

    /**
     * @param array<int, int> $arguments
     *
     * @requires PHP 8.0
     *
     * @dataProvider provideArguments80Cases
     */
    public function testArguments80(string $code, int $openIndex, int $closeIndex, array $arguments): void
    {
        $this->testArguments($code, $openIndex, $closeIndex, $arguments);
    }

    /**
     * @return iterable<int, array{string, int, int, array<int, int>}>
     */
    public static function provideArguments80Cases(): iterable
    {
        yield ['<?php class Foo { public function __construct(public ?string $param = null) {} }', 12, 23, [13 => 22]];

        yield ['<?php class Foo { public function __construct(protected ?string $param = null) {} }', 12, 23, [13 => 22]];

        yield ['<?php class Foo { public function __construct(private ?string $param = null) {} }', 12, 23, [13 => 22]];

        yield ['<?php $a = function(?\Foo\Bar $a, ?\Foo\Bar $b){};', 6, 23, [7 => 13, 15 => 22]];

        yield ['<?php function setFoo(null|int $param1, ?int $param2){}', 4, 16, [5 => 9, 11 => 15]];
    }

    /**
     * @param array<int, int> $arguments
     *
     * @requires PHP 8.1
     *
     * @dataProvider provideArguments81Cases
     */
    public function testArguments81(string $code, int $openIndex, int $closeIndex, array $arguments): void
    {
        $this->testArguments($code, $openIndex, $closeIndex, $arguments);
    }

    /**
     * @return iterable<int, array{string, int, int, array<int, int>}>
     */
    public static function provideArguments81Cases(): iterable
    {
        yield ['<?php function setFoo(\A\B&C $param1, C&D $param2){}', 4, 20, [5 => 12, 14 => 19]];
    }

    /**
     * @param array<int, int> $arguments
     *
     * @requires PHP 8.4
     *
     * @dataProvider provideArguments84Cases
     */
    public function testArguments84(string $code, int $openIndex, int $closeIndex, array $arguments): void
    {
        $this->testArguments($code, $openIndex, $closeIndex, $arguments);
    }

    /**
     * @return iterable<string, array{string, int, int, array<int, int>}>
     */
    public static function provideArguments84Cases(): iterable
    {
        yield 'property hooks' => [
            '<?php class Foo { public function __construct(private int $a { set(int $x) { $this->a = $x; } }, private int $b = 1 { set(int $x) { $this->b = $x; } }) {} }',
            12,
            77,
            [13 => 41, 43 => 76],
        ];
    }

    /**
     * @param array<int, int> $arguments
     *
     * @requires PHP 8.5
     *
     * @dataProvider provideArguments85Cases
     */
    public function testArguments85(string $code, int $openIndex, int $closeIndex, array $arguments): void
    {
        $this->testArguments($code, $openIndex, $closeIndex, $arguments);
    }

    /**
     * @return iterable<string, array{string, int, int, array<int, int>}>
     */
    public static function provideArguments85Cases(): iterable
    {
        yield 'closure as default value' => [
            '<?php function foo(Closure $x = static function ($a, $b): void {}, int $y) {}',
            4,
            32,
            [5 => 26, 28 => 31],
        ];
    }

    /**
     * @dataProvider provideArgumentInfoCases
     */
    public function testArgumentInfo(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        self::assertArgumentAnalysis($expected, $analyzer->getArgumentInfo($tokens, $openIndex, $closeIndex));
    }

    /**
     * @return iterable<int, array{string, int, int, ArgumentAnalysis}>
     */
    public static function provideArgumentInfoCases(): iterable
    {
        yield ['<?php function($a){};', 3, 3, new ArgumentAnalysis(
            '$a',
            3,
            null,
            null,
        )];

        yield ['<?php \test($a);', 4, 4, new ArgumentAnalysis(
            '$a',
            4,
            null,
            null,
        )];

        yield ['<?php function($a, $b){};', 5, 6, new ArgumentAnalysis(
            '$b',
            6,
            null,
            null,
        )];

        yield ['<?php foo($a, $b)?>', 5, 6, new ArgumentAnalysis(
            '$b',
            6,
            null,
            null,
        )];

        yield ['<?php foo($a, "b")?>', 5, 6, new ArgumentAnalysis(
            null,
            null,
            null,
            null,
        )];

        yield ['<?php function($a, $b = array(1,2), $c = 3){};', 3, 3, new ArgumentAnalysis(
            '$a',
            3,
            null,
            null,
        )];

        yield ['<?php function($a, $b = array(1, /*   */  2), $c = 3){};', 5, 18, new ArgumentAnalysis(
            '$b',
            6,
            'array(1,2)',
            null,
        )];

        yield ['<?php function($a, $b = array(1,2), $c = 3){};', 17, 22, new ArgumentAnalysis(
            '$c',
            18,
            '3',
            null,
        )];

        yield ['<?php function(array $a = array()){};', 3, 11, new ArgumentAnalysis(
            '$a',
            5,
            'array()',
            new TypeAnalysis(
                'array',
                3,
                3,
            ),
        )];

        yield ['<?php function(array &$a = array()){};', 3, 12, new ArgumentAnalysis(
            '$a',
            6,
            'array()',
            new TypeAnalysis(
                'array',
                3,
                3,
            ),
        )];

        yield ['<?php function( ... $z){};', 3, 6, new ArgumentAnalysis(
            '$z',
            6,
            null,
            null,
        )];

        yield ['<?php function(array ... $a){};', 3, 7, new ArgumentAnalysis(
            '$a',
            7,
            null,
            new TypeAnalysis(
                'array',
                3,
                3,
            ),
        )];

        yield ['<?php function(\Foo\Bar $a){};', 3, 8, new ArgumentAnalysis(
            '$a',
            8,
            null,
            new TypeAnalysis(
                '\Foo\Bar',
                3,
                6,
            ),
        )];

        yield [
            '<?php function(?\Foo\Bar $a){};', 3, 9, new ArgumentAnalysis(
                '$a',
                9,
                null,
                new TypeAnalysis(
                    '?\Foo\Bar',
                    3,
                    7,
                ),
            ),
        ];

        yield ['<?php function($a, $b = \'\'){};', 5, 10, new ArgumentAnalysis(
            '$b',
            6,
            "''",
            null,
        )];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideArgumentInfo80Cases
     */
    public function testArgumentInfo80(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $this->testArgumentInfo($code, $openIndex, $closeIndex, $expected);
    }

    /**
     * @return iterable<int, array{string, int, int, ArgumentAnalysis}>
     */
    public static function provideArgumentInfo80Cases(): iterable
    {
        yield [
            '<?php function foo(#[AnAttribute] ?string $param = null) {}',
            5,
            16,
            new ArgumentAnalysis(
                '$param',
                12,
                'null',
                new TypeAnalysis(
                    '?string',
                    9,
                    10,
                ),
            ),
        ];

        foreach (['public', 'protected', 'private'] as $visibility) {
            yield [
                \sprintf('<?php class Foo { public function __construct(%s ?string $param = null) {} }', $visibility),
                13,
                22,
                new ArgumentAnalysis(
                    '$param',
                    18,
                    'null',
                    new TypeAnalysis(
                        '?string',
                        15,
                        16,
                    ),
                ),
            ];
        }
    }

    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideArgumentInfo81Cases
     */
    public function testArgumentInfo81(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $this->testArgumentInfo($code, $openIndex, $closeIndex, $expected);
    }

    /**
     * @return iterable<int, array{string, int, int, ArgumentAnalysis}>
     */
    public static function provideArgumentInfo81Cases(): iterable
    {
        yield [
            '<?php
class Foo
{
    public function __construct(
        protected readonly ?bool $nullable = true,
    ) {}
}
',
            13,
            25,
            new ArgumentAnalysis(
                '$nullable',
                21,
                'true',
                new TypeAnalysis(
                    '?bool',
                    18,
                    19,
                ),
            ),
        ];
    }

    /**
     * @requires PHP 8.4
     *
     * @dataProvider provideArgumentInfo84Cases
     */
    public function testArgumentInfo84(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $this->testArgumentInfo($code, $openIndex, $closeIndex, $expected);
    }

    /**
     * @return iterable<string, array{string, int, int, ArgumentAnalysis}>
     */
    public static function provideArgumentInfo84Cases(): iterable
    {
        yield 'property hook without default' => [
            '<?php class Foo { public function __construct(private int $a { set(int $x) { $this->a = $x; } }) {} }',
            13,
            41,
            new ArgumentAnalysis(
                '$a',
                17,
                null,
                new TypeAnalysis('int', 15, 15),
            ),
        ];

        yield 'property hook with default' => [
            '<?php class Foo { public function __construct(private int $a = 1 { set(int $x) { $this->a = $x; } }) {} }',
            13,
            45,
            new ArgumentAnalysis(
                '$a',
                17,
                '1',
                new TypeAnalysis('int', 15, 15),
            ),
        ];

        yield 'asymmetric visibility public write' => [
            <<<'PHP'
                <?php
                class Foo {
                    public function __construct(
                        public public(set) Bar $x,
                    ) {}
                }
                PHP,
            13,
            20,
            new ArgumentAnalysis(
                '$x',
                20,
                null,
                new TypeAnalysis(
                    'Bar',
                    18,
                    18,
                ),
            ),
        ];

        yield 'asymmetric visibility protected write' => [
            <<<'PHP'
                <?php
                class Foo {
                    public function __construct(
                        public protected(set) Bar $x,
                    ) {}
                }
                PHP,
            13,
            20,
            new ArgumentAnalysis(
                '$x',
                20,
                null,
                new TypeAnalysis(
                    'Bar',
                    18,
                    18,
                ),
            ),
        ];

        yield 'asymmetric visibility private write' => [
            <<<'PHP'
                <?php
                class Foo {
                    public function __construct(
                        public private(set) Bar $x,
                    ) {}
                }
                PHP,
            13,
            20,
            new ArgumentAnalysis(
                '$x',
                20,
                null,
                new TypeAnalysis(
                    'Bar',
                    18,
                    18,
                ),
            ),
        ];
    }

    /**
     * @requires PHP 8.5
     *
     * @dataProvider provideArgumentInfo85Cases
     */
    public function testArgumentInfo85(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $this->testArgumentInfo($code, $openIndex, $closeIndex, $expected);
    }

    /**
     * @return iterable<string, array{string, int, int, ArgumentAnalysis}>
     */
    public static function provideArgumentInfo85Cases(): iterable
    {
        yield 'closure as default value' => [
            '<?php function foo(Closure $x = static function ($a, $b): void {}, int $y) {}',
            5,
            26,
            new ArgumentAnalysis(
                '$x',
                7,
                'staticfunction(,):void{}',
                new TypeAnalysis('Closure', 5, 5),
            ),
        ];

        yield 'final promoted properties' => [
            '<?php class Foo { public function __construct(
                    public final Bar $x,
                ) {} }',
            13,
            20,
            new ArgumentAnalysis(
                '$x',
                20,
                null,
                new TypeAnalysis(
                    'Bar',
                    18,
                    18,
                ),
            ),
        ];
    }

    private static function assertArgumentAnalysis(ArgumentAnalysis $expected, ArgumentAnalysis $actual): void
    {
        self::assertSame($expected->getDefault(), $actual->getDefault(), 'Default.');
        self::assertSame($expected->getName(), $actual->getName(), 'Name.');
        self::assertSame($expected->getNameIndex(), $actual->getNameIndex(), 'Name index.');
        self::assertSame($expected->hasDefault(), $actual->hasDefault(), 'Has default.');
        self::assertSame($expected->hasTypeAnalysis(), $actual->hasTypeAnalysis(), 'Has type analysis.');

        if ($expected->hasTypeAnalysis()) {
            $expectedTypeAnalysis = $expected->getTypeAnalysis();
            $actualTypeAnalysis = $actual->getTypeAnalysis();

            self::assertSame($expectedTypeAnalysis->getEndIndex(), $actualTypeAnalysis->getEndIndex(), 'Type analysis end index.');
            self::assertSame($expectedTypeAnalysis->getName(), $actualTypeAnalysis->getName(), 'Type analysis name.');
            self::assertSame($expectedTypeAnalysis->getStartIndex(), $actualTypeAnalysis->getStartIndex(), 'Type analysis start index.');
            self::assertSame($expectedTypeAnalysis->isNullable(), $actualTypeAnalysis->isNullable(), 'Type analysis nullable.');
            self::assertSame($expectedTypeAnalysis->isReservedType(), $actualTypeAnalysis->isReservedType(), 'Type analysis reserved type.');
        } else {
            self::assertNull($actual->getTypeAnalysis());
        }

        self::assertSame(serialize($expected), serialize($actual));
    }
}
