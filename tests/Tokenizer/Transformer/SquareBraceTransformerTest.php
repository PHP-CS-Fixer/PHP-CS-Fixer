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
 */
final class SquareBraceTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_ARRAY_SQUARE_BRACE_OPEN,
                CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            )
        );
    }

    /**
     * @dataProvider provideProcessCases71
     * @requires PHP 7.1
     */
    public function testProcess71($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_ARRAY_SQUARE_BRACE_OPEN,
                CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
            )
        );
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
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php function foo(array $a = [ ]) {}',
                array(
                    11 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    13 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    2 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [1, "foo"];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    6 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [[]];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    2 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    3 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                    4 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php ["foo", ["bar", "baz"]];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    10 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                    11 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php (array) [1, 2];',
                array(
                    3 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    8 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php [1,2][$x];',
                array(
                    1 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    5 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
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
                '<?php "foo"[1];',
            ),
        );
    }

    public function provideProcessCases71()
    {
        return array(
            array(
                '<?php [$a, $b, $c] = [1, 2, 3];',
                array(
                    1 => CT::T_DESTRUCTURING_SQUARE_BRACE_OPEN,
                    9 => CT::T_DESTRUCTURING_SQUARE_BRACE_CLOSE,
                    13 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    21 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
            array(
                '<?php $a = [1]; $a[] = 2; $a[1] = 3;',
                array(
                    5 => CT::T_ARRAY_SQUARE_BRACE_OPEN,
                    7 => CT::T_ARRAY_SQUARE_BRACE_CLOSE,
                ),
            ),
        );
    }
}
