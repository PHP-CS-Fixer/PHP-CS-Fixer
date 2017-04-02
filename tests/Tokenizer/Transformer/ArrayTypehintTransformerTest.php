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

use PhpCsFixer\Test\AbstractTransformerTestCase;
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
                T_ARRAY,
                CT::T_ARRAY_TYPEHINT,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php
$a = array(1, 2, 3);
function foo (array /** @type array */ $bar)
{
}',
                array(
                    5 => T_ARRAY,
                    22 => CT::T_ARRAY_TYPEHINT,
                ),
            ),
        );
    }
}
