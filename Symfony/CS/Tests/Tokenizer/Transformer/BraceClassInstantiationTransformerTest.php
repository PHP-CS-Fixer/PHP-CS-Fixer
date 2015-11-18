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

use Symfony\CS\Test\AbstractTransformerTestCase;

/**
 * @author Sebastiaans Stok <s.stok@rollerscapes.net>
 *
 * @internal
 */
final class BraceClassInstantiationTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens)
    {
        $this->doTest($source, $expectedTokens);
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php echo (new Process())->getOutput();',
                array(
                    3 => 'CT_BRACE_CLASS_INSTANTIATION_OPEN',
                    9 => 'CT_BRACE_CLASS_INSTANTIATION_CLOSE',
                ),
            ),
            array(
                '<?php echo (new Process())::getOutput();',
                array(
                    3 => 'CT_BRACE_CLASS_INSTANTIATION_OPEN',
                    9 => 'CT_BRACE_CLASS_INSTANTIATION_CLOSE',
                ),
            ),
        );
    }
}
