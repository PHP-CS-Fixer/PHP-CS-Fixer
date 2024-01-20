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
use PhpCsFixer\Tokenizer\Analyzer\SwitchAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\SwitchAnalyzer
 */
final class SwitchAnalyzerTest extends TestCase
{
    /**
     * @param array<int> $indices
     *
     * @dataProvider provideColonCases
     */
    public function testColon(string $code, array $indices): void
    {
        $tokens = Tokens::fromCode($code);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            self::assertSame(
                \in_array($index, $indices, true),
                SwitchAnalyzer::belongsToSwitch($tokens, $index),
                sprintf('Index %d failed check.', $index)
            );
        }
    }

    /**
     * @return iterable<array{string, array<int>}>
     */
    public static function provideColonCases(): iterable
    {
        yield 'ternary operator' => [
            '<?php $x ? 1 : 0;',
            [],
        ];

        yield 'alternative syntax' => [
            '<?php if(true): 3; endif;',
            [],
        ];

        yield 'label' => [
            '<?php gotoHere: echo "here";',
            [],
        ];

        yield 'switch' => [
            '<?php
                switch ($value1) {
                    case 1: return 2;
                    case 3: return 4;
                    default: return 5;
                }
            ',
            [13, 23, 31],
        ];

        yield 'switch with alternative syntax' => [
            '<?php
                switch ($value1):
                    case 1: return 2;
                    default: return 3;
                    case 4: return 5;
                endswitch;
            ',
            [7, 12, 20, 30],
        ];
    }

    public function testWithEmptyTokens(): void
    {
        $tokensWithEmpty = Tokens::fromCode(<<<'PHP'
            <?php
            switch/* to remove */($value1) {
                case 1: return 2;
            }
            PHP);
        $tokensWithEmpty->clearAt(2);

        $tokensWithoutEmpty = clone $tokensWithEmpty;
        $tokensWithoutEmpty->clearEmptyTokens();

        self::assertStringNotContainsString('to remove', $tokensWithEmpty->generateCode());
        self::assertStringNotContainsString('to remove', $tokensWithoutEmpty->generateCode());

        self::assertSame(
            $tokensWithEmpty->count(),
            $tokensWithoutEmpty->count() + 1
        );

        self::assertTrue(SwitchAnalyzer::belongsToSwitch($tokensWithEmpty, 12));
        self::assertTrue(SwitchAnalyzer::belongsToSwitch($tokensWithoutEmpty, 11));
    }
}
