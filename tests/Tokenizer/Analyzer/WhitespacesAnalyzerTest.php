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
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(WhitespacesAnalyzer::class)]
final class WhitespacesAnalyzerTest extends TestCase
{
    /**
     * @dataProvider provideIndentCases
     */
    #[DataProvider('provideIndentCases')]
    public function testIndent(string $code, string $indent, int $index): void
    {
        $tokens = Tokens::fromCode($code);

        self::assertSame($indent, WhitespacesAnalyzer::detectIndent($tokens, $index));
    }

    /**
     * @return iterable<int, array{string, string, int}>
     */
    public static function provideIndentCases(): iterable
    {
        yield ['<?php function foo() { return true; }', '', 10];

        yield [
            '<?php
                        function foo() { return true; }
            ',
            '                        ',
            8,
        ];

        $code = '<?php
            // wrong indent
                function foo() { /* foo */ return    true; }
            ';
        $tokens = Tokens::fromCode($code);

        foreach (range(4, $tokens->count() - 2) as $index) {
            yield [$code, '                ', $index];
        }
    }
}
