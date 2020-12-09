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
     * @param string $source
     * @param int[]  $expectedTrue
     *
     * @dataProvider provideIsClassyInvocationCases
     */
    public function testGotoLabelAnalyzerTest($source, array $expectedTrue)
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

    public function provideIsClassyInvocationCases()
    {
        $tests = [
            'no candidates' => [
                '<?php
                    $a = \InvalidArgumentException::class;
                    $this->fixer->configure($legacy ? [$statement] : [1]);
                ',
                [],
            ],
            'handle goto labels 1' => [
                '<?php
                    beginning:
                    echo $guard?1:2;',
                [3],
            ],
            'handle goto labels 2' => [
                '<?php
                    function A(){}
                    beginning:
                    echo $guard?1:2;',
                [11],
            ],
            'handle goto labels 3' => [
                '<?php
                    echo 1;
                    beginning:
                    echo $guard?1:2;',
                [8],
            ],
            'handle goto labels 4' => [
                '<?php
                    echo 1;
                    {
                        beginning:
                        echo $guard?1:2;
                    }
                ',
                [10],
            ],
        ];

        foreach ($tests as $index => $test) {
            yield $index => $test;
        }

        if (\PHP_VERSION_ID >= 80000) {
            yield [
                '<?php array_fill(start_index: 0, num: 100, value: 50);', [],
            ];
        }
    }
}
