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

use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\FunctionReturnTypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tests\TestCase;

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

        $this->assertSame(serialize($expected), serialize($analyzer->getFunctionArguments($tokens, $methodIndex)));
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

        $this->assertSame(serialize($expected), serialize($analyzer->getFunctionReturnType($tokens, $methodIndex)));
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
                    null,
                    null,
                    null
                ),
            ]],
            ['<?php function($a, $b){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null,
                    null,
                    null
                ),
                '$b' => new ArgumentAnalysis(
                    '$b',
                    6,
                    null,
                    null,
                    null,
                    null
                ),
            ]],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    3,
                    null,
                    null,
                    null,
                    null
                ),
                '$b' => new ArgumentAnalysis(
                    '$b',
                    6,
                    'array(1,2)',
                    null,
                    null,
                    null
                ),
                '$c' => new ArgumentAnalysis(
                    '$c',
                    18,
                    '3',
                    null,
                    null,
                    null
                ),
            ]],
            ['<?php function(array $a = array()){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    5,
                    'array()',
                    'array',
                    3,
                    3
                ),
            ]],
            ['<?php function(array ... $a){};', 1, [
                '$a' => new ArgumentAnalysis(
                    '$a',
                    7,
                    null,
                    'array',
                    3,
                    3
                ),
            ]],
        ];
    }

    public function provideFunctionsWithReturnTypeCases()
    {
        return [
            ['<?php function(){};', 1, null],
            ['<?php function($a): array {};', 1, new FunctionReturnTypeAnalysis('array', 7, 7)],
            ['<?php function($a): \Foo\Bar {};', 1, new FunctionReturnTypeAnalysis('\Foo\Bar', 7, 10)],
        ];
    }
}
