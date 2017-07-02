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
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                T_USE,
                CT::T_USE_LAMBDA,
                CT::T_USE_TRAIT,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php use Foo;',
                array(
                    1 => T_USE,
                ),
            ),
            array(
                '<?php $foo = function() use ($bar) {};',
                array(
                    9 => CT::T_USE_LAMBDA,
                ),
            ),
            array(
                '<?php class Foo { use Bar; }',
                array(
                    7 => CT::T_USE_TRAIT,
                ),
            ),
            array(
                '<?php namespace Aaa; use Bbb; class Foo { use Bar; function baz() { $a=1; return function () use ($a) {}; } }',
                array(
                    6 => T_USE,
                    17 => CT::T_USE_TRAIT,
                    42 => CT::T_USE_LAMBDA,
                ),
            ),
        );
    }
}
