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
     * @param string $code
     * @param int    $openIndex
     * @param int    $closeIndex
     * @param array  $arguments
     *
     * @dataProvider provideArgumentsCases
     */
    public function testArguments($code, $openIndex, $closeIndex, array $arguments)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        $this->assertSame(count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        $this->assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

    /**
     * @param string $code
     * @param int    $openIndex
     * @param int    $closeIndex
     * @param array $expected
     * @dataProvider provideArgumentsInfo
     */
    public function testArgumentInfo($code, $openIndex, $closeIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        $this->assertSame($expected, $analyzer->getArgumentInfo($tokens, $openIndex, $closeIndex));
    }

    public function provideArgumentsCases()
    {
        return [
            ['<?php fnc();', 2, 3, []],
            ['<?php fnc($a);', 2, 4, [3 => 3]],
            ['<?php fnc($a, $b);', 2, 7, [3 => 3, 5 => 6]],
            ['<?php fnc($a, $b = array(1,2), $c = 3);', 2, 23, [3 => 3, 5 => 15, 17 => 22]],
        ];
    }

    public function provideArgumentsInfo()
    {
        return [
            ['<?php function($a){};', 3, 3, [
                'default' => '',
                'name' => '$a',
                'name_index' => 3,
                'type' => '',
                'type_index_start' => -1,
                'type_index_end' => -1,
            ]],
            ['<?php function($a, $b){};', 5, 6, [
                'default' => '',
                'name' => '$b',
                'name_index' => 6,
                'type' => '',
                'type_index_start' => -1,
                'type_index_end' => -1,
            ]],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 3, 3, [
                'default' => '',
                'name' => '$a',
                'name_index' => 3,
                'type' => '',
                'type_index_start' => -1,
                'type_index_end' => -1,
            ]],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 5, 15, [
                'default' => 'array(1,2)',
                'name' => '$b',
                'name_index' => 6,
                'type' => '',
                'type_index_start' => -1,
                'type_index_end' => -1,
            ]],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 17, 22, [
                'default' => '3',
                'name' => '$c',
                'name_index' => 18,
                'type' => '',
                'type_index_start' => -1,
                'type_index_end' => -1,
            ]],
            ['<?php function(int $a = 3){};', 3, 9, [
                'default' => '3',
                'name' => '$a',
                'name_index' => 5,
                'type' => 'int',
                'type_index_start' => 3,
                'type_index_end' => 3,
            ]],
            ['<?php function(int ... $a){};', 3, 7, [
                'default' => '',
                'name' => '$a',
                'name_index' => 7,
                'type' => 'int',
                'type_index_start' => 3,
                'type_index_end' => 3,
            ]],
        ];
    }
}