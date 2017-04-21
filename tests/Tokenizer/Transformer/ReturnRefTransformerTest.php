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
 * @covers \PhpCsFixer\Tokenizer\Transformer\ReturnRefTransformer
 */
final class ReturnRefTransformerTest extends AbstractTransformerTestCase
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
                CT::T_RETURN_REF,
            ]
        );
    }

    public function provideProcessCases()
    {
        return [
            [
                '<?php function & foo(): array { return []; }',
                [
                    3 => CT::T_RETURN_REF,
                ],
            ],
            [
                '<?php $a = 1 & 2;',
            ],
            [
                '<?php function fnc(array & $arr) {}',
            ],
        ];
    }
}
