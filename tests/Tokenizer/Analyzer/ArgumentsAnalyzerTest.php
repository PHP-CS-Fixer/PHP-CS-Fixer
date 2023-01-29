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

        static::assertSame(\count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        static::assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

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
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        static::assertSame(\count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        static::assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

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
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        static::assertSame(\count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        static::assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

    public static function provideArguments81Cases(): iterable
    {
        yield ['<?php function setFoo(\A\B&C $param1, C&D $param2){}', 4, 20, [5 => 12, 14 => 19]];
    }

    /**
     * @dataProvider provideArgumentsInfoCases
     */
    public function testArgumentInfo(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        self::assertArgumentAnalysis($expected, $analyzer->getArgumentInfo($tokens, $openIndex, $closeIndex));
    }

    public static function provideArgumentsInfoCases(): iterable
    {
        yield ['<?php function($a){};', 3, 3, new ArgumentAnalysis(
            '$a',
            3,
            null,
            null
        )];

        yield ['<?php \test($a);', 4, 4, new ArgumentAnalysis(
            '$a',
            4,
            null,
            null
        )];

        yield ['<?php function($a, $b){};', 5, 6, new ArgumentAnalysis(
            '$b',
            6,
            null,
            null
        )];

        yield ['<?php foo($a, $b)?>', 5, 6, new ArgumentAnalysis(
            '$b',
            6,
            null,
            null
        )];

        yield ['<?php function($a, $b = array(1,2), $c = 3){};', 3, 3, new ArgumentAnalysis(
            '$a',
            3,
            null,
            null
        )];

        yield ['<?php function($a, $b = array(1, /*   */  2), $c = 3){};', 5, 18, new ArgumentAnalysis(
            '$b',
            6,
            'array(1,2)',
            null
        )];

        yield ['<?php function($a, $b = array(1,2), $c = 3){};', 17, 22, new ArgumentAnalysis(
            '$c',
            18,
            '3',
            null
        )];

        yield ['<?php function(array $a = array()){};', 3, 11, new ArgumentAnalysis(
            '$a',
            5,
            'array()',
            new TypeAnalysis(
                'array',
                3,
                3
            )
        )];

        yield ['<?php function(array &$a = array()){};', 3, 12, new ArgumentAnalysis(
            '$a',
            6,
            'array()',
            new TypeAnalysis(
                'array',
                3,
                3
            )
        )];

        yield ['<?php function( ... $z){};', 3, 6, new ArgumentAnalysis(
            '$z',
            6,
            null,
            null
        )];

        yield ['<?php function(array ... $a){};', 3, 7, new ArgumentAnalysis(
            '$a',
            7,
            null,
            new TypeAnalysis(
                'array',
                3,
                3
            )
        )];

        yield ['<?php function(\Foo\Bar $a){};', 3, 8, new ArgumentAnalysis(
            '$a',
            8,
            null,
            new TypeAnalysis(
                '\Foo\Bar',
                3,
                6
            )
        )];

        yield [
            '<?php function(?\Foo\Bar $a){};', 3, 9, new ArgumentAnalysis(
                '$a',
                9,
                null,
                new TypeAnalysis(
                    '?\Foo\Bar',
                    3,
                    7
                )
            ),
        ];

        yield ['<?php function($a, $b = \'\'){};', 5, 10, new ArgumentAnalysis(
            '$b',
            6,
            "''",
            null
        )];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideArgumentsInfo80Cases
     */
    public function testArgumentInfo80(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        self::assertArgumentAnalysis($expected, $analyzer->getArgumentInfo($tokens, $openIndex, $closeIndex));
    }

    public static function provideArgumentsInfo80Cases(): iterable
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
                    10
                )
            ),
        ];

        foreach (['public', 'protected', 'private'] as $visibility) {
            yield [
                sprintf('<?php class Foo { public function __construct(%s ?string $param = null) {} }', $visibility),
                13,
                22,
                new ArgumentAnalysis(
                    '$param',
                    18,
                    'null',
                    new TypeAnalysis(
                        '?string',
                        15,
                        16
                    )
                ),
            ];
        }
    }

    /**
     * @requires PHP 8.1
     *
     * @dataProvider provideArgumentsInfo81Cases
     */
    public function testArgumentInfo81(string $code, int $openIndex, int $closeIndex, ArgumentAnalysis $expected): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        self::assertArgumentAnalysis($expected, $analyzer->getArgumentInfo($tokens, $openIndex, $closeIndex));
    }

    public static function provideArgumentsInfo81Cases(): iterable
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
                    19
                )
            ),
        ];
    }

    private static function assertArgumentAnalysis(ArgumentAnalysis $expected, ArgumentAnalysis $actual): void
    {
        static::assertSame($expected->getDefault(), $actual->getDefault(), 'Default.');
        static::assertSame($expected->getName(), $actual->getName(), 'Name.');
        static::assertSame($expected->getNameIndex(), $actual->getNameIndex(), 'Name index.');
        static::assertSame($expected->hasDefault(), $actual->hasDefault(), 'Has default.');
        static::assertSame($expected->hasTypeAnalysis(), $actual->hasTypeAnalysis(), 'Has type analysis.');

        if ($expected->hasTypeAnalysis()) {
            $expectedTypeAnalysis = $expected->getTypeAnalysis();
            $actualTypeAnalysis = $actual->getTypeAnalysis();

            static::assertSame($expectedTypeAnalysis->getEndIndex(), $actualTypeAnalysis->getEndIndex(), 'Type analysis end index.');
            static::assertSame($expectedTypeAnalysis->getName(), $actualTypeAnalysis->getName(), 'Type analysis name.');
            static::assertSame($expectedTypeAnalysis->getStartIndex(), $actualTypeAnalysis->getStartIndex(), 'Type analysis start index.');
            static::assertSame($expectedTypeAnalysis->isNullable(), $actualTypeAnalysis->isNullable(), 'Type analysis nullable.');
            static::assertSame($expectedTypeAnalysis->isReservedType(), $actualTypeAnalysis->isReservedType(), 'Type analysis reserved type.');
        } else {
            static::assertNull($actual->getTypeAnalysis());
        }

        static::assertSame(serialize($expected), serialize($actual));
    }
}
