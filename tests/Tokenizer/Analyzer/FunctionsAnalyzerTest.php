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

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
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
     * @param array $expected
     * @dataProvider provideFunctionsWithArguments
     */
    public function testFunctionArgumentInfo($code, $methodIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        $this->assertSame($expected, $analyzer->getFunctionArguments($tokens, $methodIndex));
    }

    /**
     * @param string $code
     * @param int    $methodIndex
     * @param array $expected
     * @dataProvider provideFunctionsWithReturnType
     */
    public function testFunctionReturnTypeInfo($code, $methodIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new FunctionsAnalyzer();

        $this->assertSame($expected, $analyzer->getFunctionReturnType($tokens, $methodIndex));
    }

    public function provideFunctionsWithArguments()
    {
        return [
            ['<?php function(){};', 1, []],
            ['<?php function($a){};', 1, [
                '$a' => [
                    'default' => '',
                    'name' => '$a',
                    'name_index' => 3,
                    'type' => '',
                    'type_index_start' => -1,
                    'type_index_end' => -1,
                ]
            ]],
            ['<?php function($a, $b){};', 1, [
                '$a' => [
                    'default' => '',
                    'name' => '$a',
                    'name_index' => 3,
                    'type' => '',
                    'type_index_start' => -1,
                    'type_index_end' => -1,
                ],
                '$b' => [
                    'default' => '',
                    'name' => '$b',
                    'name_index' => 6,
                    'type' => '',
                    'type_index_start' => -1,
                    'type_index_end' => -1,
                ],
            ]],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 1, [
                '$a' => [
                    'default' => '',
                    'name' => '$a',
                    'name_index' => 3,
                    'type' => '',
                    'type_index_start' => -1,
                    'type_index_end' => -1,
                ],
                '$b' => [
                    'default' => 'array(1,2)',
                    'name' => '$b',
                    'name_index' => 6,
                    'type' => '',
                    'type_index_start' => -1,
                    'type_index_end' => -1,
                ],
                '$c' => [
                    'default' => '3',
                    'name' => '$c',
                    'name_index' => 18,
                    'type' => '',
                    'type_index_start' => -1,
                    'type_index_end' => -1,
                ]
            ]],
            ['<?php function(array $a = array()){};', 1, [
                '$a' => [
                    'default' => 'array()',
                    'name' => '$a',
                    'name_index' => 5,
                    'type' => 'array',
                    'type_index_start' => 3,
                    'type_index_end' => 3,
                ]
            ]],
            ['<?php function(array ... $a){};', 1, [
                '$a' => [
                    'default' => '',
                    'name' => '$a',
                    'name_index' => 7,
                    'type' => 'array',
                    'type_index_start' => 3,
                    'type_index_end' => 3,
                ]
            ]],
        ];
    }

    public function provideFunctionsWithReturnType()
    {
        return [
            ['<?php function(){};', 1, []],
            ['<?php function($a): array {};', 1, [
                'type' => 'array',
                'start_index' => 7,
                'end_index' => 7,
            ]],
            ['<?php function($a): \Foo\Bar {};', 1, [
                'type' => '\Foo\Bar',
                'start_index' => 7,
                'end_index' => 10,
            ]],
        ];
    }
}