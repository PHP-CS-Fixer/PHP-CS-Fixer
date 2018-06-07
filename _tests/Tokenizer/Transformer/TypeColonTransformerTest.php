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
     * @param string $source
     *
     * @dataProvider provideProcessCases
     * @requires PHP 7.0
     */
    public function testProcess($source, array $expectedTokens = [])
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_TYPE_COLON,
            ]
        );
    }

    public function provideProcessCases()
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
        ];
    }
}
