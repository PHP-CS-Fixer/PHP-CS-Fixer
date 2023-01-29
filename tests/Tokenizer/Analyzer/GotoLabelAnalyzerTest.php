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
 */
final class GotoLabelAnalyzerTest extends TestCase
{
    /**
     * @param int[] $expectedTrue
     *
     * @dataProvider provideIsClassyInvocationCases
     */
    public function testGotoLabelAnalyzerTest(string $source, array $expectedTrue): void
    {
        $tokens = Tokens::fromCode($source);
        $analyzer = new GotoLabelAnalyzer();

        foreach ($tokens as $index => $isClassy) {
            static::assertSame(
                \in_array($index, $expectedTrue, true),
                $analyzer->belongsToGoToLabel($tokens, $index)
            );
        }
    }

    public static function provideIsClassyInvocationCases(): iterable
    {
        yield from [
            'no candidates' => [
                '<?php
                    $a = \InvalidArgumentException::class;
                    $this->fixer->configure($legacy ? [$statement] : [1]);
                ',
                [],
            ],
            'after php tag' => [
                '<?php
                    beginning:
                    echo $guard?1:2;',
                [3],
            ],
            'after closing brace' => [
                '<?php
                    function A(){}
                    beginning:
                    echo $guard?1:2;',
                [11],
            ],
            'after statement' => [
                '<?php
                    echo 1;
                    beginning:
                    echo $guard?1:2;',
                [8],
            ],
            'after opening brace' => [
                '<?php
                    echo 1;
                    {
                        beginning:
                        echo $guard?1:2;
                    }
                ',
                [10],
            ],
            'after use statements' => [
                '<?php
use Bar1;
use const Bar2;
use function Bar3;
Bar1:
Bar2:
Bar3:
',
                [21, 24, 27],
            ],
        ];

        if (\PHP_VERSION_ID >= 8_00_00) {
            yield [
                '<?php array_fill(start_index: 0, num: 100, value: 50);', [],
            ];
        }
    }
}
