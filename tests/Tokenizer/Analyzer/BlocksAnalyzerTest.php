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
use PhpCsFixer\Tokenizer\Analyzer\BlocksAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\BlocksAnalyzer
 */
final class BlocksAnalyzerTest extends TestCase
{
    /**
     * @dataProvider provideBlocksCases
     */
    public function testBlocks(string $code, int $openIndex, int $closeIndex): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new BlocksAnalyzer();

        static::assertTrue($analyzer->isBlock($tokens, $openIndex, $closeIndex));
    }

    public function provideBlocksCases(): array
    {
        return [
            ['<?php foo(1);', 2, 4],
            ['<?php foo((1));', 3, 5],
            ['<?php foo((1));', 2, 6],
            ['<?php foo(1, 2, 3);', 2, 10],
            ['<?php foo(1, bar(2, 3), 4);', 2, 16],
            ['<?php $foo["bar"];', 2, 4],
            ['<?php [1, 2, 3];', 1, 9],
            ['<?php $foo = function ($x) { return $x + 10; };', 7, 9],
            ['<?php $foo = function ($x) { return $x + 10; };', 11, 22],
            ['<?php list($a, $b, $c) = [1, 2, 3];', 2, 10],
            ['<?php list($a, $b, $c) = [1, 2, 3];', 14, 22],
            ['<?php list($a, $b, $c) = array(1, 2, 3);', 15, 23],
        ];
    }

    /**
     * @dataProvider provideNonBlocksCases
     */
    public function testNonBlocks(string $code, ?int $openIndex, ?int $closeIndex, bool $isBlock = false): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new BlocksAnalyzer();

        static::assertSame($isBlock, $analyzer->isBlock($tokens, $openIndex, $closeIndex));
    }

    public function provideNonBlocksCases(): array
    {
        return [
            ['<?php foo(1);', null, 4],
            ['<?php foo(1);', 2, null],
            ['<?php foo(1);', 1000, 4],
            ['<?php foo(1);', 2, 1000],
            ['<?php foo(1);', 1, 4],
            ['<?php foo(1);', 3, 4],
            ['<?php foo(1);', 2, 3],
            ['<?php foo((1));', 2, 5],
            ['<?php foo((1));', 3, 6],
            ['<?php $fn = fn($x) => $x + 10;', 6, 8, true],
        ];
    }
}
