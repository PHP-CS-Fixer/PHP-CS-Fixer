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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\AbstractControlCaseStructuresAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\EnumAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\MatchAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ControlCaseStructuresAnalyzer;
use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ControlCaseStructuresAnalyzer
 *
 * @internal
 */
final class ControlCaseStructuresAnalyzerTest extends TestCase
{
    /**
     * @param array<int, AbstractControlCaseStructuresAnalysis> $expectedAnalyses
     *
     * @dataProvider provideFindControlStructuresCases
     */
    public function testFindControlStructures(array $expectedAnalyses, string $source): void
    {
        $tokens = Tokens::fromCode($source);
        $analyses = iterator_to_array(ControlCaseStructuresAnalyzer::findControlStructures($tokens, [\T_SWITCH]));

        self::assertCount(\count($expectedAnalyses), $analyses);

        foreach ($expectedAnalyses as $index => $expectedAnalysis) {
            \assert(\array_key_exists($index, $analyses));
            self::assertAnalysis($expectedAnalysis, $analyses[$index]);
        }
    }

    /**
     * @return iterable<array{0: array<int, AbstractControlCaseStructuresAnalysis>, 1: string, 2?: int}>
     */
    public static function provideFindControlStructuresCases(): iterable
    {
        yield 'two cases' => [
            [1 => new SwitchAnalysis(1, 7, 46, [new CaseAnalysis(9, 12), new CaseAnalysis(36, 39)], null)],
            '<?php switch ($foo) {
                case 1: $x = bar() ? 1 : 0; return true;
                case 2: return false;
            }',
        ];

        yield 'case without code' => [
            [1 => new SwitchAnalysis(1, 7, 34, [new CaseAnalysis(9, 12), new CaseAnalysis(19, 22), new CaseAnalysis(24, 27)], null)],
            '<?php switch ($foo) {
                case 1: return true;
                case 2:
                case 3: return false;
            }',
        ];

        yield 'advanced cases' => [
            [
                1 => new SwitchAnalysis(
                    1,
                    7,
                    132,
                    [
                        new CaseAnalysis(17, 22),
                        new CaseAnalysis(29, 40),
                        new CaseAnalysis(47, 53),
                        new CaseAnalysis(60, 71),
                        new CaseAnalysis(78, 125),
                    ],
                    new DefaultAnalysis(9, 10)
                ),
            ],
            '<?php switch (true) {
                default: return 0;
                case ("a"): return 1;
                case [1, 2, 3]: return 2;
                case getValue($foo): return 3;
                case getValue2($foo)["key"]->bar: return 4;
                case $a->$b::$c->${$d}->${$e}::foo(function ($x) { return $x * 2 + 2; })->$g::$h: return 5;
            }',
        ];

        yield 'two case and default' => [
            [1 => new SwitchAnalysis(1, 7, 38, [new CaseAnalysis(9, 12), new CaseAnalysis(19, 22)], new DefaultAnalysis(29, 30))],
            '<?php switch ($foo) { case 10: return true; case 100: return false; default: return -1; }',
        ];

        yield 'two case and default with semicolon instead of colon' => [
            [1 => new SwitchAnalysis(1, 7, 38, [new CaseAnalysis(9, 12), new CaseAnalysis(19, 22)], new DefaultAnalysis(29, 30))],
            '<?php switch ($foo) { case 10; return true; case 100; return false; default; return -1; }',
        ];

        yield 'ternary operator in case' => [
            [1 => new SwitchAnalysis(1, 7, 39, [new CaseAnalysis(9, 22), new CaseAnalysis(29, 32)], null)],
            '<?php switch ($foo) { case ($bar ? 10 : 20): return true; case 100: return false; }',
        ];

        yield 'nested switch' => [
            [
                1 => new SwitchAnalysis(1, 7, 67, [new CaseAnalysis(9, 12), new CaseAnalysis(57, 60)], null),
                14 => new SwitchAnalysis(14, 20, 52, [new CaseAnalysis(22, 25), new CaseAnalysis(32, 35), new CaseAnalysis(42, 45)], null),
            ],
            '<?php switch ($foo) { case 10:
                switch ($bar) { case "a": return "b"; case "c": return "d"; case "e": return "f"; }
                return;
                case 100: return false; }',
        ];

        yield 'switch in case' => [
            [
                1 => new SwitchAnalysis(1, 7, 98, [new CaseAnalysis(9, 81), new CaseAnalysis(88, 91)], null),
                25 => new SwitchAnalysis(25, 31, 63, [new CaseAnalysis(33, 36), new CaseAnalysis(43, 46), new CaseAnalysis(53, 56)], null),
            ],
            '<?php
switch ($foo) {
    case (
        array_sum(array_map(function ($x) { switch ($bar) { case "a": return "b"; case "c": return "d"; case "e": return "f"; } }, [1, 2, 3]))
    ):
        return true;
    case 100:
        return false;
}
',
        ];

        yield 'alternative syntax' => [
            [1 => new SwitchAnalysis(1, 7, 30, [new CaseAnalysis(9, 12), new CaseAnalysis(19, 22)], null)],
            '<?php switch ($foo) : case 10: return true; case 100: return false; endswitch;',
        ];

        yield 'alternative syntax with closing tag' => [
            [1 => new SwitchAnalysis(1, 7, 31, [new CaseAnalysis(9, 12), new CaseAnalysis(19, 22)], null)],
            '<?php switch ($foo) : case 10: return true; case 100: return false; endswitch ?>',
        ];

        yield 'alternative syntax nested' => [
            [
                1 => new SwitchAnalysis(1, 7, 69, [new CaseAnalysis(9, 12), new CaseAnalysis(58, 61)], null),
                14 => new SwitchAnalysis(14, 20, 53, [new CaseAnalysis(22, 25), new CaseAnalysis(32, 35), new CaseAnalysis(42, 45)], null),
            ],
            '<?php switch ($foo) : case 10:
                switch ($bar) : case "a": return "b"; case "c": return "d"; case "e": return "f"; endswitch;
                return;
                case 100: return false; endswitch;',
        ];

        yield 'alternative syntax nested with mixed colon/semicolon' => [
            [
                1 => new SwitchAnalysis(1, 7, 69, [new CaseAnalysis(9, 12), new CaseAnalysis(58, 61)], null),
                14 => new SwitchAnalysis(14, 20, 53, [new CaseAnalysis(22, 25), new CaseAnalysis(32, 35), new CaseAnalysis(42, 45)], null),
            ],
            '<?php switch ($foo) : case 10;
                switch ($bar) : case "a": return "b"; case "c"; return "d"; case "e": return "f"; endswitch;
                return;
                case 100: return false; endswitch;',
        ];

        yield 'alternative syntax nested with closing tab and mixed colon/semicolon' => [
            [
                1 => new SwitchAnalysis(1, 7, 70, [new CaseAnalysis(9, 12), new CaseAnalysis(58, 61)], null),
                14 => new SwitchAnalysis(14, 20, 53, [new CaseAnalysis(22, 25), new CaseAnalysis(32, 35), new CaseAnalysis(42, 45)], null),
            ],
            '<?php switch ($foo) : case 10;
                switch ($bar) : case "a": return "b"; case "c"; return "d"; case "e": return "f"; endswitch;
                return;
                case 100: return false; endswitch ?>  <?php echo 1;',
        ];

        $expected = [
            1 => new SwitchAnalysis(
                1,
                6,
                22,
                [
                    new CaseAnalysis(8, 11),
                ],
                new DefaultAnalysis(16, 17)
            ),
        ];

        $code = '<?php switch($a) {
case 1:
    break;
default:
    break;
}';

        yield 'case :' => [$expected, $code];

        $code = str_replace('case 1:', 'case 1;', $code);
        $code = str_replace('default:', 'DEFAULT;', $code);

        yield 'case ;' => [$expected, $code];

        yield 'no default, comments' => [
            [
                1 => new SwitchAnalysis(
                    1,
                    6,
                    18,
                    [
                        new CaseAnalysis(8, 12),
                    ],
                    null
                ),
            ],
            '<?php switch($a) {
case 1/* 1 */:
    break;
/* 2 */}',
        ];

        yield 'ternary case' => [
            [
                2 => new SwitchAnalysis(
                    2,
                    8,
                    27,
                    [
                        new CaseAnalysis(10, 22),
                    ],
                    null
                ),
            ],
            '<?php
                switch ($a) {
                    case $b ? "c" : "d" ;
                        break;
                }',
        ];

        yield 'nested' => [
            [
                1 => new SwitchAnalysis(
                    1,
                    8,
                    55,
                    [
                        new CaseAnalysis(10, 13),
                        new CaseAnalysis(18, 21),
                        new CaseAnalysis(47, 50),
                    ],
                    null
                ),
                23 => new SwitchAnalysis(
                    23,
                    30,
                    42,
                    [
                        new CaseAnalysis(32, 35),
                    ],
                    null
                ),
            ],
            '<?php
switch(foo()) {
    CASE 1:

        break;
    case 2:
        switch(bar()) {
            case 1:
                echo 1;
        }

        break;
    case 3:
        break;
}
',
        ];

        yield 'alternative syntax 2' => [
            [
                3 => new SwitchAnalysis(
                    3,
                    8,
                    32,
                    [
                        new CaseAnalysis(10, 13),
                    ],
                    null
                ),
            ],
            '<?php /* */ switch ($foo):
case 1:
    $foo = new class {};
    break;
endswitch ?>',
        ];

        yield [
            [],
            '<?php',
        ];

        yield 'function with return type' => [
            [1 => new SwitchAnalysis(1, 7, 43, [new CaseAnalysis(9, 12), new CaseAnalysis(33, 36)], null)],
            '<?php switch ($foo) { case 10: function foo($x): int {}; return true; case 100: return false; }',
        ];

        yield 'function with nullable parameter' => [
            [1 => new SwitchAnalysis(1, 7, 43, [new CaseAnalysis(9, 12), new CaseAnalysis(33, 36)], null)],
            '<?php switch ($foo) { case 10: function foo(?int $x) {}; return true; case 100: return false; }',
        ];
    }

    /**
     * @param array<int, AbstractControlCaseStructuresAnalysis> $expectedAnalyses
     * @param list<int>                                         $types
     *
     * @requires PHP 8.1
     *
     * @dataProvider provideFindControlStructuresPhp81Cases
     */
    public function testFindControlStructuresPhp81(array $expectedAnalyses, string $source, array $types): void
    {
        $tokens = Tokens::fromCode($source);
        $analyses = iterator_to_array(ControlCaseStructuresAnalyzer::findControlStructures($tokens, $types));

        self::assertCount(\count($expectedAnalyses), $analyses);

        foreach ($expectedAnalyses as $index => $expectedAnalysis) {
            \assert(\array_key_exists($index, $analyses));
            self::assertAnalysis($expectedAnalysis, $analyses[$index]);
        }
    }

    /**
     * @return iterable<int, array{array<int, AbstractControlCaseStructuresAnalysis>, string, list<int>}>
     */
    public static function provideFindControlStructuresPhp81Cases(): iterable
    {
        $switchAnalysis = new SwitchAnalysis(1, 6, 26, [new CaseAnalysis(8, 11)], new DefaultAnalysis(18, 19));
        $enumAnalysis = new EnumAnalysis(28, 35, 51, [new CaseAnalysis(37, 41), new CaseAnalysis(46, 49)]);
        $matchAnalysis = new MatchAnalysis(57, 63, 98, new DefaultAnalysis(89, 91));

        $code = '<?php
switch($a) {
    case 1:
        echo 2;
    default:
        echo 1;
}

enum Suit: string {
    case Hearts = "foo";
    case Hearts2;
}

$expressionResult = match ($condition) {
    1, 2 => foo(),
    3, 4 => bar(),
    default => baz(),
};
';

        yield [
            [
                1 => $switchAnalysis,
            ],
            $code,
            [\T_SWITCH],
        ];

        yield [
            [
                1 => $switchAnalysis,
                28 => $enumAnalysis,
            ],
            $code,
            [\T_SWITCH, FCT::T_ENUM],
        ];

        yield [
            [
                57 => $matchAnalysis,
            ],
            $code,
            [FCT::T_MATCH],
        ];
    }

    public function testNoSupportedControlStructure(): void
    {
        $tokens = Tokens::fromCode('<?php if(time() > 0){ echo 1; }');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Unexpected type "%d".', \T_IF));

        // we use `iterator_to_array` to ensure generator is consumed and it has possibility to raise exception
        iterator_to_array(ControlCaseStructuresAnalyzer::findControlStructures($tokens, [\T_IF]));
    }

    private static function assertAnalysis(AbstractControlCaseStructuresAnalysis $expectedAnalysis, AbstractControlCaseStructuresAnalysis $analysis): void
    {
        self::assertSame($expectedAnalysis->getIndex(), $analysis->getIndex(), 'index');
        self::assertSame($expectedAnalysis->getOpenIndex(), $analysis->getOpenIndex(), 'open index');
        self::assertSame($expectedAnalysis->getCloseIndex(), $analysis->getCloseIndex(), 'close index');
        self::assertInstanceOf(\get_class($expectedAnalysis), $analysis);

        if ($expectedAnalysis instanceof MatchAnalysis || $expectedAnalysis instanceof SwitchAnalysis) {
            $expectedDefault = $expectedAnalysis->getDefaultAnalysis();
            $actualDefault = $analysis->getDefaultAnalysis(); // @phpstan-ignore-line already type checked against expected

            if (null === $expectedDefault) {
                self::assertNull($actualDefault, 'default not null');
            } else {
                self::assertSame($expectedDefault->getIndex(), $actualDefault->getIndex(), 'default index');
                self::assertSame($expectedDefault->getColonIndex(), $actualDefault->getColonIndex(), 'default colon index');
            }
        }

        if ($expectedAnalysis instanceof EnumAnalysis || $expectedAnalysis instanceof SwitchAnalysis) {
            $expectedCases = $expectedAnalysis->getCases();
            $actualCases = $analysis->getCases(); // @phpstan-ignore-line already type checked against expected

            self::assertCount(\count($expectedCases), $actualCases);

            foreach ($expectedCases as $i => $expectedCase) {
                self::assertSame($expectedCase->getIndex(), $actualCases[$i]->getIndex(), 'case index');
                self::assertSame($expectedCase->getColonIndex(), $actualCases[$i]->getColonIndex(), 'case colon index');
            }
        }

        self::assertSame(
            serialize($expectedAnalysis),
            serialize($analysis)
        );
    }
}
