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
 * @author Sebastiaans Stok <s.stok@rollerscapes.net>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\BraceClassInstantiationTransformer
 */
final class BraceClassInstantiationTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens)
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php echo (new Process())->getOutput();',
                array(
                    3 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    9 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
            array(
                '<?php echo (new Process())::getOutput();',
                array(
                    3 => CT::T_BRACE_CLASS_INSTANTIATION_OPEN,
                    9 => CT::T_BRACE_CLASS_INSTANTIATION_CLOSE,
                ),
            ),
        );
    }
}
