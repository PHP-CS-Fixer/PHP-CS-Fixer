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
 * @covers \PhpCsFixer\Tokenizer\Transformer\ArrayTypehintTransformer
 */
final class ArrayTypehintTransformerTest extends AbstractTransformerTestCase
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
                T_ARRAY,
                CT::T_ARRAY_TYPEHINT,
            ]
        );
    }

    public function provideProcessCases(): array
    {
        return [
            [
                '<?php
$a = array(1, 2, 3);
function foo (array /** @type array */ $bar)
{
}',
                [
                    5 => T_ARRAY,
                    22 => CT::T_ARRAY_TYPEHINT,
                ],
            ],
            [
                '<?php
$a = array(1, 2, 3);
$fn = fn(array /** @type array */ $bar) => null;',
                [
                    5 => T_ARRAY,
                    23 => CT::T_ARRAY_TYPEHINT,
                ],
            ],
        ];
    }
}
