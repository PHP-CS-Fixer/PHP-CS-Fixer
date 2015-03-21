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
            array(
                '<?php [];',
                array(
                    1 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    2 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php [1, "foo"];',
                array(
                    1 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    6 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php [[]];',
                array(
                    1 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    2 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    3 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                    4 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php ["foo", ["bar", "baz"]];',
                array(
                    1 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    5 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    10 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                    11 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php (array) [1, 2];',
                array(
                    3 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    8 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php [1,2][$x];',
                array(
                    1 => 'CT_ARRAY_SQUARE_BRACE_OPEN',
                    5 => 'CT_ARRAY_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php array();',
            ),
            array(
                '<?php $x[] = 1;',
            ),
            array(
                '<?php $x[1];',
            ),
            array(
                '<?php $x [ 1 ];',
            ),
            array(
                '<?php ${"x"}[1];',
            ),
            array(
                '<?php FOO[1];',
            ),
            array(
                '<?php array("foo")[1];',
            ),
            array(
                '<?php foo()[1];',
            ),
            array(
                '<?php \'foo\'[1];',
            ),
            array(
                '<?php "foo$bar"[1];',
            ),
        );
    }
}
