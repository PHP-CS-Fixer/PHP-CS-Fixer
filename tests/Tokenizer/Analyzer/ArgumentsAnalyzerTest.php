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
     *
     * @dataProvider provideArgumentsCases
     */
    public function testArguments($code, $openIndex, $closeIndex, array $arguments)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        static::assertSame(\count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        static::assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

    /**
     * @param string $code
     * @param int    $openIndex
     * @param int    $closeIndex
     * @param array  $expected
     *
     * @dataProvider provideArgumentsInfoCases
     */
    public function testArgumentInfo($code, $openIndex, $closeIndex, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        static::assertSame(
            serialize($expected),
            serialize($analyzer->getArgumentInfo($tokens, $openIndex, $closeIndex))
        );
    }

    public function provideArgumentsCases()
    {
        return [
            ['<?php function(){};', 2, 3, []],
            ['<?php foo();', 2, 3, []],
            ['<?php function($a){};', 2, 4, [3 => 3]],
            ['<?php \foo($a);', 3, 5, [4 => 4]],
            ['<?php function($a, $b){};', 2, 7, [3 => 3, 5 => 6]],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 2, 23, [3 => 3, 5 => 15, 17 => 22]],
        ];
    }

    public function provideArgumentsInfoCases()
    {
        return [
            ['<?php function($a){};', 3, 3, new ArgumentAnalysis(
                '$a',
                3,
                null,
                null
            )],
            ['<?php \test($a);', 4, 4, new ArgumentAnalysis(
                '$a',
                4,
                null,
                null
            )],
            ['<?php function($a, $b){};', 5, 6, new ArgumentAnalysis(
                '$b',
                6,
                null,
                null
            )],
            ['<?php foo($a, $b)?>', 5, 6, new ArgumentAnalysis(
                '$b',
                6,
                null,
                null
            )],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 3, 3, new ArgumentAnalysis(
                '$a',
                3,
                null,
                null
            )],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 5, 15, new ArgumentAnalysis(
                '$b',
                6,
                'array(1,2)',
                null
            )],
            ['<?php function($a, $b = array(1,2), $c = 3){};', 17, 22, new ArgumentAnalysis(
                '$c',
                18,
                '3',
                null
            )],
            ['<?php function(array $a = array()){};', 3, 11, new ArgumentAnalysis(
                '$a',
                5,
                'array()',
                new TypeAnalysis(
                    'array',
                    3,
                    3
                )
            )],
            ['<?php function(array &$a = array()){};', 3, 12, new ArgumentAnalysis(
                '$a',
                6,
                'array()',
                new TypeAnalysis(
                    'array',
                    3,
                    3
                )
            )],
            ['<?php function(array ... $a){};', 3, 7, new ArgumentAnalysis(
                '$a',
                7,
                null,
                new TypeAnalysis(
                    'array',
                    3,
                    3
                )
            )],
            ['<?php function(\Foo\Bar $a){};', 3, 8, new ArgumentAnalysis(
                '$a',
                8,
                null,
                new TypeAnalysis(
                    '\Foo\Bar',
                    3,
                    6
                )
            )],
        ];
    }

    /**
     * @param string $code
     * @param int    $openIndex
     * @param int    $closeIndex
     *
     * @requires PHP 7.3
     * @dataProvider provideArguments73Cases
     */
    public function testArguments73($code, $openIndex, $closeIndex, array $arguments)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new ArgumentsAnalyzer();

        static::assertSame(\count($arguments), $analyzer->countArguments($tokens, $openIndex, $closeIndex));
        static::assertSame($arguments, $analyzer->getArguments($tokens, $openIndex, $closeIndex));
    }

    public function provideArguments73Cases()
    {
        return [
            ['<?php foo($a,);', 2, 5, [3 => 3]],
            ['<?php foo($a,/**/);', 2, 6, [3 => 3]],
            ['<?php foo($a(1,2,3,4,5),);', 2, 16, [3 => 14]],
            ['<?php foo($a(1,2,3,4,5,),);', 2, 17, [3 => 15]],
            ['<?php foo($a(1,2,3,4,5,),);', 4, 15, [5 => 5, 7 => 7, 9 => 9, 11 => 11, 13 => 13]],
            ['<?php bar($a, $b , ) ;', 2, 10, [3 => 3, 5 => 7]],
        ];
    }
}
