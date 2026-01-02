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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AlternativeSyntaxAnalyzerTest extends TestCase
{
    /**
     * @param list<int> $expectedPositives
     *
     * @dataProvider provideBelongsToAlternativeSyntaxCases
     */
    public function testBelongsToAlternativeSyntax(array $expectedPositives, string $source): void
    {
        $tokens = Tokens::fromCode($source);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            self::assertSame(
                \in_array($index, $expectedPositives, true),
                (new AlternativeSyntaxAnalyzer())->belongsToAlternativeSyntax($tokens, $index),
                '@ index: '.$index,
            );
        }
    }

    /**
     * @return iterable<string, array{list<int>, string}>
     */
    public static function provideBelongsToAlternativeSyntaxCases(): iterable
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

        yield 'if, elseif, else' => [
            [6, 17, 25],
            '<?php if ($condition): echo 1; elseif($a): echo 2; else: echo 3; endif;',
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

    /**
     * @dataProvider provideItFindsTheEndOfAnAlternativeSyntaxBlockCases
     */
    public function testItFindsTheEndOfAnAlternativeSyntaxBlock(string $code, int $startIndex, int $expectedResult): void
    {
        $analyzer = new AlternativeSyntaxAnalyzer();

        self::assertSame(
            $expectedResult,
            $analyzer->findAlternativeSyntaxBlockEnd(
                Tokens::fromCode($code),
                $startIndex,
            ),
        );
    }

    /**
     * @return iterable<int, array{string, int, int}>
     */
    public static function provideItFindsTheEndOfAnAlternativeSyntaxBlockCases(): iterable
    {
        yield ['<?php if ($foo): foo(); endif;', 1, 13];

        yield ['<?php if ($foo): foo(); else: bar(); endif;', 1, 13];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); endif;', 1, 13];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); endif;', 13, 25];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); else: baz(); endif;', 13, 25];

        yield ['<?php if ($foo): foo(); else: bar(); endif;', 13, 21];

        yield ['<?php for (;;): foo(); endfor;', 1, 14];

        yield ['<?php foreach ($foo as $bar): foo(); endforeach;', 1, 17];

        yield ['<?php while ($foo): foo(); endwhile;', 1, 13];

        yield ['<?php switch ($foo): case 1: foo(); endswitch;', 1, 18];

        $nested = <<<'PHP'
            <?php
            switch (foo()):
                case 1:
                    switch (foo2()):
                        case 2:
                            if (bar()) {

                            }
                            switch (foo2()):
                                case 4:
                                {
                                    switch (foo3()) {
                                        case 4:
                                        {

                                        }
                                    }
                                }
                            endswitch;
                    endswitch;
                case 2:
                    switch (foo5()) {
                        case 4:
                            echo 1;
                    }
            endswitch;
            PHP;

        yield [$nested, 1, 113];

        yield [$nested, 15, 83];

        yield [$nested, 41, 80];

        $nestedWithHtml = <<<'PHP'
            <?php if (1): ?>
                <div></div>
            <?php else: ?>
                <?php if (2): ?>
                    <div></div>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
            <?php endif; ?>
            PHP;

        yield [$nestedWithHtml, 1, 11];

        yield [$nestedWithHtml, 11, 38];

        yield [$nestedWithHtml, 17, 27];

        yield [$nestedWithHtml, 27, 33];
    }

    /**
     * @dataProvider provideItThrowsOnInvalidAlternativeSyntaxBlockStartIndexCases
     */
    public function testItThrowsOnInvalidAlternativeSyntaxBlockStartIndex(string $code, int $startIndex, string $expectedMessage): void
    {
        $tokens = Tokens::fromCode($code);

        $analyzer = new AlternativeSyntaxAnalyzer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $analyzer->findAlternativeSyntaxBlockEnd($tokens, $startIndex);
    }

    /**
     * @return iterable<int, array{string, int, string}>
     */
    public static function provideItThrowsOnInvalidAlternativeSyntaxBlockStartIndexCases(): iterable
    {
        yield ['<?php if ($foo): foo(); endif;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); endif;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); endif;', 999, 'There is no token at index 999.'];

        yield ['<?php if ($foo): foo(); else: bar(); endif;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); else: bar(); endif;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); else: bar(); endif;', 999, 'There is no token at index 999.'];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); endif;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); endif;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); endif;', 999, 'There is no token at index 999.'];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); else: baz(); endif;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); else: baz(); endif;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php if ($foo): foo(); elseif ($bar): bar(); else: baz(); endif;', 999, 'There is no token at index 999.'];

        yield ['<?php for (;;): foo(); endfor;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php for (;;): foo(); endfor;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php for (;;): foo(); endfor;', 999, 'There is no token at index 999.'];

        yield ['<?php foreach ($foo as $bar): foo(); endforeach;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php foreach ($foo as $bar): foo(); endforeach;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php foreach ($foo as $bar): foo(); endforeach;', 999, 'There is no token at index 999.'];

        yield ['<?php while ($foo): foo(); endwhile;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php while ($foo): foo(); endwhile;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php while ($foo): foo(); endwhile;', 999, 'There is no token at index 999.'];

        yield ['<?php switch ($foo): case 1: foo(); endswitch;', 0, 'Token at index 0 is not the start of an alternative syntax block.'];

        yield ['<?php switch ($foo): case 1: foo(); endswitch;', 2, 'Token at index 2 is not the start of an alternative syntax block.'];

        yield ['<?php switch ($foo): case 1: foo(); endswitch;', 999, 'There is no token at index 999.'];
    }
}
