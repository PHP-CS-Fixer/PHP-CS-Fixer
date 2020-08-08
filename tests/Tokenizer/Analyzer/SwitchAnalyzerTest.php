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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\SwitchAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers  \PhpCsFixer\Tokenizer\Analyzer\SwitchAnalyzer
 *
 * @internal
 */
final class SwitchAnalyzerTest extends TestCase
{
    public function testForNotSwitch()
    {
        $analyzer = new SwitchAnalyzer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Index 3 is not "switch".');

        $analyzer->getSwitchAnalysis(Tokens::fromCode('<?php $a;$b;$c;'), 3);
    }

    /**
     * @dataProvider provideGettingSwitchAnalysisCases
     *
     * @param mixed $code
     * @param mixed $index
     */
    public function testGettingSwitchAnalysis(SwitchAnalysis $expected, $code, $index)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new SwitchAnalyzer();

        static::assertSame(serialize($expected), serialize(($analyzer->getSwitchAnalysis($tokens, $index))));
    }

    public static function provideGettingSwitchAnalysisCases()
    {
        yield 'two cases' => [
            new SwitchAnalysis(7, 29, [new CaseAnalysis(12), new CaseAnalysis(22)]),
            '<?php switch ($foo) { case 10: return true; case 100: return false; }',
            1,
        ];

        yield 'two case and default' => [
            new SwitchAnalysis(7, 37, [new CaseAnalysis(12), new CaseAnalysis(22), new CaseAnalysis(30)]),
            '<?php switch ($foo) { case 10: return true; case 100: return false; default: return -1;}',
            1,
        ];

        yield 'ternary operator in case' => [
            new SwitchAnalysis(7, 39, [new CaseAnalysis(22), new CaseAnalysis(32)]),
            '<?php switch ($foo) { case ($bar ? 10 : 20): return true; case 100: return false; }',
            1,
        ];

        yield 'nested switch' => [
            new SwitchAnalysis(7, 67, [new CaseAnalysis(12), new CaseAnalysis(60)]),
            '<?php switch ($foo) { case 10:
                switch ($bar) { case "a": return "b"; case "c": return "d"; case "e": return "f"; }
                return;
                case 100: return false; }',
            1,
        ];

        yield 'switch in case' => [
            new SwitchAnalysis(7, 98, [new CaseAnalysis(81), new CaseAnalysis(91)]),
            '<?php switch ($foo) { case (
                array_sum(array_map(function ($x) { switch ($bar) { case "a": return "b"; case "c": return "d"; case "e": return "f"; } }, [1, 2, 3]))
            ): return true; case 100: return false; }',
            1,
        ];

        yield 'alternative syntax' => [
            new SwitchAnalysis(7, 30, [new CaseAnalysis(12), new CaseAnalysis(22)]),
            '<?php switch ($foo) : case 10: return true; case 100: return false; endswitch;',
            1,
        ];

        yield 'alternative syntax with closing tag' => [
            new SwitchAnalysis(7, 29, [new CaseAnalysis(12), new CaseAnalysis(22)]),
            '<?php switch ($foo) : case 10: return true; case 100: return false; endswitch ?>',
            1,
        ];

        yield 'alternative syntax nested' => [
            new SwitchAnalysis(7, 69, [new CaseAnalysis(12), new CaseAnalysis(61)]),
            '<?php switch ($foo) : case 10:
                switch ($bar) : case "a": return "b"; case "c": return "d"; case "e": return "f"; endswitch;
                return;
                case 100: return false; endswitch;',
            1,
        ];

        if (PHP_VERSION >= 70000) {
            yield 'function with return type' => [
                new SwitchAnalysis(7, 43, [new CaseAnalysis(12), new CaseAnalysis(36)]),
                '<?php switch ($foo) { case 10: function foo($x): int {}; return true; case 100: return false; }',
                1,
            ];
        }

        if (PHP_VERSION >= 70100) {
            yield 'function with nullable parameter' => [
                new SwitchAnalysis(7, 43, [new CaseAnalysis(12), new CaseAnalysis(36)]),
                '<?php switch ($foo) { case 10: function foo(?int $x) {}; return true; case 100: return false; }',
                1,
            ];
        }
    }
}
