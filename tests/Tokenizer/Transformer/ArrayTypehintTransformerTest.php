<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Test\AbstractTransformerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ArrayTypehintTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest($source, $expectedTokens);
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
                    5 => 'T_ARRAY',
                    22 => 'CT_ARRAY_TYPEHINT',
                ),
            ),
        );
    }
}
