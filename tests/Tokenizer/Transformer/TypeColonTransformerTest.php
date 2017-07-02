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
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_TYPE_COLON,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php function foo(): array { return []; }',
                array(
                    6 => CT::T_TYPE_COLON,
                ),
            ),
            array(
                '<?php function & foo(): array { return []; }',
                array(
                    8 => CT::T_TYPE_COLON,
                ),
            ),
            array(
                '<?php interface F { public function foo(): array; }',
                array(
                    14 => CT::T_TYPE_COLON,
                ),
            ),
            array(
                '<?php $a=1; $f = function () : array {};',
                array(
                    15 => CT::T_TYPE_COLON,
                ),
            ),
            array(
                '<?php $a=1; $f = function () use($a) : array {};',
                array(
                    20 => CT::T_TYPE_COLON,
                ),
            ),
            array(
                '<?php
                    $a = 1 ? [] : [];
                    $b = 1 ? fnc() : [];
                    $c = 1 ?: [];
                ',
            ),
        );
    }
}
