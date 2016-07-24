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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ArrayDestructuringTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     * @requires PHP 7.1
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->markTestIncomplete('Implementation is not there yet');
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                'CT_DESTRUCTURING_SQUARE_BRACE_OPEN',
                'CT_DESTRUCTURING_SQUARE_BRACE_CLOSE',
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php [$a, $b, $c] = [1, 2, 3];',
                array(
                    1 => 'CT_DESTRUCTURING_SQUARE_BRACE_OPEN',
                    9 => 'CT_DESTRUCTURING_SQUARE_BRACE_CLOSE',
                ),
            ),
            array(
                '<?php $a = [1]; $a[] = 2; $a[1] = 3;',
            ),
        );
    }
}
