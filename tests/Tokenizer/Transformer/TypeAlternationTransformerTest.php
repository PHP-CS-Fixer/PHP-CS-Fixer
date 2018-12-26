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
    public function testProcess($source, array $expectedTokens = [])
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_TYPE_ALTERNATION,
            ]
        );
    }

    public function provideProcessCases()
    {
        return [
            'no namespace' => [
                '<?php try {} catch (ExceptionType1 | ExceptionType2 | ExceptionType3 $e) {}',
                [
                    11 => CT::T_TYPE_ALTERNATION,
                    15 => CT::T_TYPE_ALTERNATION,
                ],
            ],
            'comments & spacing' => [
                "<?php try {/* 1 */} catch (/* 2 */ExceptionType1/* 3 */\t\n|  \n\t/* 4 */\n\tExceptionType2 \$e) {}",
                [
                    14 => CT::T_TYPE_ALTERNATION,
                ],
            ],
            'native namespace only' => [
                '<?php try {} catch (\ExceptionType1 | \ExceptionType2 | \ExceptionType3 $e) {}',
                [
                    12 => CT::T_TYPE_ALTERNATION,
                    17 => CT::T_TYPE_ALTERNATION,
                ],
            ],
            'namespaces' => [
                '<?php try {} catch (A\ExceptionType1 | \A\ExceptionType2 | \A\B\C\ExceptionType3 $e) {}',
                [
                    13 => CT::T_TYPE_ALTERNATION,
                    20 => CT::T_TYPE_ALTERNATION,
                ],
            ],
            'do not fix cases' => [
                '<?php
                    echo 2 | 4;
                    echo "aaa" | "bbb";
                    echo F_OK | F_ERR;
                    echo foo(F_OK | F_ERR);
                    // try {} catch (ExceptionType1 | ExceptionType2) {}
                ',
            ],
        ];
    }
}
