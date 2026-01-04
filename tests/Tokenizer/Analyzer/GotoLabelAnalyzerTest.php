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
use PhpCsFixer\Tokenizer\Analyzer\GotoLabelAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\GotoLabelAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GotoLabelAnalyzerTest extends TestCase
{
    /**
     * @param list<int> $expectedTrue
     *
     * @dataProvider provideGotoLabelCases
     */
    public function testGotoLabel(string $source, array $expectedTrue): void
    {
        $tokens = Tokens::fromCode($source);
        $analyzer = new GotoLabelAnalyzer();

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            self::assertSame(
                \in_array($index, $expectedTrue, true),
                $analyzer->belongsToGoToLabel($tokens, $index),
            );
        }
    }

    /**
     * @return iterable<string, array{string, list<int>}>
     */
    public static function provideGotoLabelCases(): iterable
    {
        yield 'no candidates' => [
            '<?php
                    $a = \InvalidArgumentException::class;
                    $this->fixer->configure($legacy ? [$statement] : [1]);
                ',
            [],
        ];

        yield 'after php tag' => [
            '<?php
                    beginning:
                    echo $guard?1:2;',
            [3],
        ];

        yield 'after closing brace' => [
            '<?php
                    function A(){}
                    beginning:
                    echo $guard?1:2;',
            [11],
        ];

        yield 'after statement' => [
            '<?php
                    echo 1;
                    beginning:
                    echo $guard?1:2;',
            [8],
        ];

        yield 'after opening brace' => [
            '<?php
                    echo 1;
                    {
                        beginning:
                        echo $guard?1:2;
                    }
                ',
            [10],
        ];

        yield 'after use statements' => [
            '<?php
use Bar1;
use const Bar2;
use function Bar3;
Bar1:
Bar2:
Bar3:
',
            [21, 24, 27],
        ];
    }

    /**
     * @param list<int> $expectedTrue
     *
     * @dataProvider provideGotoLabel80Cases
     *
     * @requires PHP 8.0
     */
    public function testGotoLabel80(string $source, array $expectedTrue): void
    {
        $this->testGotoLabel($source, $expectedTrue);
    }

    /**
     * @return iterable<int, array{string, list<int>}>
     */
    public static function provideGotoLabel80Cases(): iterable
    {
        yield [
            '<?php array_fill(start_index: 0, num: 100, value: 50);',
            [],
        ];
    }
}
