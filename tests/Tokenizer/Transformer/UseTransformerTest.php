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
 * @covers \PhpCsFixer\Tokenizer\Transformer\UseTransformer
 */
final class UseTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = [])
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                T_USE,
                CT::T_USE_LAMBDA,
                CT::T_USE_TRAIT,
            ]
        );
    }

    public function provideProcessCases()
    {
        return [
            [
                '<?php use Foo;',
                [
                    1 => T_USE,
                ],
            ],
            [
                '<?php $foo = function() use ($bar) {};',
                [
                    9 => CT::T_USE_LAMBDA,
                ],
            ],
            [
                '<?php class Foo { use Bar; }',
                [
                    7 => CT::T_USE_TRAIT,
                ],
            ],
            [
                '<?php namespace Aaa; use Bbb; class Foo { use Bar; function baz() { $a=1; return function () use ($a) {}; } }',
                [
                    6 => T_USE,
                    17 => CT::T_USE_TRAIT,
                    42 => CT::T_USE_LAMBDA,
                ],
            ],
        ];
    }
}
