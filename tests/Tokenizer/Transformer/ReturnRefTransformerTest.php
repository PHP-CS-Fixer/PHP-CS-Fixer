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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\ReturnRefTransformer
 */
final class ReturnRefTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_RETURN_REF,
            ]
        );
    }

    public static function provideProcessCases(): iterable
    {
        yield [
            '<?php function & foo(): array { return []; }',
            [
                3 => CT::T_RETURN_REF,
            ],
        ];

        yield [
            '<?php $a = 1 & 2;',
        ];

        yield [
            '<?php function fnc(array & $arr) {}',
        ];

        yield [
            '<?php fn &(): array => [];',
            [
                3 => CT::T_RETURN_REF,
            ],
        ];

        yield [
            '<?php $a = 1 & 2;',
        ];

        yield [
            '<?php fn (array & $arr) => null;',
        ];
    }
}
