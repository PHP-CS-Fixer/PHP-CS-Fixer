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
 * @covers \PhpCsFixer\Tokenizer\Transformer\TypeColonTransformer
 */
final class TypeColonTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int|string> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_TYPE_COLON,
            ]
        );
    }

    public function provideProcessCases(): array
    {
        return [
            [
                '<?php function foo(): array { return []; }',
                [
                    6 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php function & foo(): array { return []; }',
                [
                    8 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php interface F { public function foo(): array; }',
                [
                    14 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php $a=1; $f = function () : array {};',
                [
                    15 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php $a=1; $f = function () use($a) : array {};',
                [
                    20 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php
                    $a = 1 ? [] : [];
                    $b = 1 ? fnc() : [];
                    $c = 1 ?: [];
                ',
            ],
            [
                '<?php fn(): array => [];',
                [
                    4 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php fn & (): array => [];',
                [
                    7 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php $a=1; $f = fn () : array => [];',
                [
                    15 => CT::T_TYPE_COLON,
                ],
            ],
        ];
    }

    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcess81Cases
     *
     * @requires PHP 8.1
     */
    public function testProcess81(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_TYPE_COLON,
            ]
        );
    }

    public function provideProcess81Cases(): array
    {
        return [
            [
                '<?php enum Foo: int {}',
                [
                    4 => CT::T_TYPE_COLON,
                ],
            ],
            [
                '<?php enum Foo /** */ : int {}',
                [
                    7 => CT::T_TYPE_COLON,
                ],
            ],
        ];
    }
}
