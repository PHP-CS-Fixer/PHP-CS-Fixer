<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer\Transformer;

use Symfony\CS\Tests\Tokenizer\AbstractTransformerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ArraySquareBraceTransformerTest extends AbstractTransformerTestBase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->makeTest($source, $expectedTokens);
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php $a = array(); $a[] = 0; $a[1] = 2;',
            ),
            array(
                '<?php $a = [1, 2, 3];',
                array(
                    5 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    13 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php function foo(array $a = [ ]);',
                array(
                    11 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    13 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
        );
    }
}
