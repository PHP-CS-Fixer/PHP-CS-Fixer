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
 * @covers \PhpCsFixer\Tokenizer\Transformer\TypeAlternationTransformer
 */
final class TypeAlternationTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     * @requires PHP 7.1
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_TYPE_ALTERNATION,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php try {} catch (ExceptionType1 | ExceptionType2 | ExceptionType3 $e) {}',
                array(
                    11 => CT::T_TYPE_ALTERNATION,
                    15 => CT::T_TYPE_ALTERNATION,
                ),
            ),
            array(
                '<?php
                    echo 2 | 4;
                    echo "aaa" | "bbb";
                    echo F_OK | F_ERR;
                ',
            ),
        );
    }
}
