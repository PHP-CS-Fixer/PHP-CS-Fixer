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

        self::assertTrue($analyzer->isBlock($tokens, $openIndex, $closeIndex));
    }

    /**
     * @return iterable<int, array{string, int, int}>
     */
    public static function provideBlocksCases(): iterable
    {
        yield ['<?php foo(1);', 2, 4];

        yield ['<?php foo((1));', 3, 5];

        yield ['<?php foo((1));', 2, 6];

        yield ['<?php foo(1, 2, 3);', 2, 10];

        yield ['<?php foo(1, bar(2, 3), 4);', 2, 16];

        yield ['<?php $foo["bar"];', 2, 4];

        yield ['<?php [1, 2, 3];', 1, 9];

        yield ['<?php $foo = function ($x) { return $x + 10; };', 7, 9];

        yield ['<?php $foo = function ($x) { return $x + 10; };', 11, 22];

        yield ['<?php list($a, $b, $c) = [1, 2, 3];', 2, 10];

        yield ['<?php list($a, $b, $c) = [1, 2, 3];', 14, 22];

        yield ['<?php list($a, $b, $c) = array(1, 2, 3);', 15, 23];

        yield ['<?php $fn = fn($x) => $x + 10;', 6, 8];
    }

    /**
     * @dataProvider provideNonBlocksCases
     */
    public function testNonBlocks(string $code, int $openIndex, int $closeIndex): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new BlocksAnalyzer();

        self::assertFalse($analyzer->isBlock($tokens, $openIndex, $closeIndex));
    }

    /**
     * @return iterable<int, array{string, int, int}>
     */
    public static function provideNonBlocksCases(): iterable
    {
        yield ['<?php foo(1);', 1, 4];

        yield ['<?php foo(1);', 3, 4];

        yield ['<?php foo(1);', 2, 3];

        yield ['<?php foo((1));', 2, 5];

        yield ['<?php foo((1));', 3, 6];
    }

    /**
     * @dataProvider provideInvalidIndexCases
     */
    public function testInvalidIndex(string $code, int $openIndex, int $closeIndex): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new BlocksAnalyzer();

        $this->expectException(\InvalidArgumentException::class);

        $analyzer->isBlock($tokens, $openIndex, $closeIndex);
    }

    /**
     * @return iterable<int, array{string, int, int}>
     */
    public static function provideInvalidIndexCases(): iterable
    {
        yield ['<?php foo(1);', 1_000, 4];

        yield ['<?php foo(1);', 2, 1_000];
    }
}
