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
use PhpCsFixer\Tokenizer\Analyzer\AlternativeSyntaxAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\AlternativeSyntaxAnalyzer
 */
final class AlternativeSyntaxAnalyzerTest extends TestCase
{
    /**
     * @param int[] $expectedPositives
     *
     * @dataProvider provideBelongsToAlternativeSyntaxCases
     */
    public function testBelongsToAlternativeSyntax(array $expectedPositives, string $source): void
    {
        $tokens = Tokens::fromCode($source);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            static::assertSame(
                \in_array($index, $expectedPositives, true),
                (new AlternativeSyntaxAnalyzer())->belongsToAlternativeSyntax($tokens, $index)
            );
        }
    }

    public function provideBelongsToAlternativeSyntaxCases(): iterable
    {
        yield 'declare' => [
            [7],
            '<?php declare(ticks=1):enddeclare;',
        ];

        yield 'for' => [
            [20],
            '<?php for($i = 0; $i < 10; $i++): echo $i; endfor;',
        ];

        yield 'foreach' => [
            [17],
            '<?php foreach([1, 2, 3] as $i): echo $i; endforeach;',
        ];

        yield 'if' => [
            [6, 14],
            '<?php if ($condition): echo 1; else: echo 2; endif;',
        ];

        yield 'switch' => [
            [6],
            '<?php switch ($value): default: echo 4; endswitch;',
        ];

        yield 'while' => [
            [5],
            '<?php while(true): echo "na"; endwhile;',
        ];

        yield 'multiple expressions' => [
            [7, 15, 51],
            '<?php
                if ($condition1): echo 1; else: echo 2; endif;
                somelabel: echo 3;
                echo $condition2 ? 4 : 5;
                if ($condition3): echo 6; endif;
            ',
        ];
    }
}
