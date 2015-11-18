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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ClassConstantTransformerTest extends AbstractTransformerTestCase
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
                '<?php echo X::class',
                array(
                    5 => 'CT_CLASS_CONSTANT',
                ),
            ),
        );
    }
}
