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
     * @dataProvider provideColonCases
     */
    public function testColon(bool $belongsToSwitch, string $code, int $index): void
    {
        $tokens = Tokens::fromCode($code);

        self::assertTrue($tokens[$index]->equals(':'));
        self::assertSame($belongsToSwitch, SwitchAnalyzer::belongsToSwitch($tokens, $index));
    }

    /**
     * @return iterable<array{bool, string, int}>
     */
    public static function provideColonCases(): iterable
    {
        yield [
            false,
            '<?php $x ? 1 : 0;',
            7,
        ];

        yield [
            false,
            '<?php if(true): 3; endif;',
            5,
        ];

        yield [
            false,
            '<?php gotoHere: echo "here";',
            2,
        ];

        $switchCode = '<?php
            switch (true) {
                case 1: return 2;
                case 3: return 4;
                default: return 5;;
            }';

        foreach ([13, 23, 31] as $index) {
            yield [true, $switchCode, $index];
        }
    }
}
