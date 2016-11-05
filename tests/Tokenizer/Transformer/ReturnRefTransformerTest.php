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
final class ReturnRefTransformerTest extends AbstractTransformerTestCase
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
                CT::T_RETURN_REF,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php function & foo(): array { return []; }',
                array(
                    3 => CT::T_RETURN_REF,
                ),
            ),
            array(
                '<?php $a = 1 & 2;',
            ),
            array(
                '<?php function fnc(array & $arr) {}',
            ),
        );
    }
}
