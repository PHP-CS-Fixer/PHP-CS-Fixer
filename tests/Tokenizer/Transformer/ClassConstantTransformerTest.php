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

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\ClassConstantTransformer
 */
final class ClassConstantTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     * @requires PHP 5.5
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_CLASS_CONSTANT,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php echo X::class;',
                array(
                    5 => CT::T_CLASS_CONSTANT,
                ),
            ),
            array(
                '<?php echo X::cLaSS;',
                array(
                    5 => CT::T_CLASS_CONSTANT,
                ),
            ),
            array(
                '<?php echo X::bar;',
            ),
            array(
                '<?php class X{}',
            ),
        );
    }
}
