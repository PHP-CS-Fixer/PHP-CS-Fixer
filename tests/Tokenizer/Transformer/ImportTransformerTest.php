<?php

declare(strict_types=1);

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
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\ImportTransformer
 */
final class ImportTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, int> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens = []): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                T_CONST,
                CT::T_CONST_IMPORT,
                T_FUNCTION,
                CT::T_FUNCTION_IMPORT,
            ]
        );
    }

    public static function provideProcessCases(): array
    {
        return [
            [
                '<?php const FOO = 1;',
                [
                    1 => T_CONST,
                ],
            ],
            [
                '<?php use Foo; const FOO = 1;',
                [
                    6 => T_CONST,
                ],
            ],
            [
                '<?php class Foo { const BAR = 1; }',
                [
                    7 => T_CONST,
                ],
            ],
            [
                '<?php use const Foo\\BAR;',
                [
                    3 => CT::T_CONST_IMPORT,
                ],
            ],
            [
                '<?php function foo() {}',
                [
                    1 => T_FUNCTION,
                ],
            ],
            [
                '<?php $a = function () {};',
                [
                    5 => T_FUNCTION,
                ],
            ],
            [
                '<?php class Foo { function foo() {} }',
                [
                    7 => T_FUNCTION,
                ],
            ],
            [
                '<?php function & foo() {}',
                [
                    1 => T_FUNCTION,
                ],
            ],
            [
                '<?php use function Foo\\bar;',
                [
                    3 => CT::T_FUNCTION_IMPORT,
                ],
            ],
            [
                '<?php use Foo\ { function Bar };',
                [
                    8 => CT::T_FUNCTION_IMPORT,
                ],
            ],
            [
                '<?php use Foo\ {
                    function F1,
                    const Constants\C1,
                    function Functions\F2,
                    const C2,
                    function F3,
                    const C3,
                };',
                [
                    8 => CT::T_FUNCTION_IMPORT,
                    13 => CT::T_CONST_IMPORT,
                    20 => CT::T_FUNCTION_IMPORT,
                    27 => CT::T_CONST_IMPORT,
                    32 => CT::T_FUNCTION_IMPORT,
                    37 => CT::T_CONST_IMPORT,
                ],
            ],
        ];
    }
}
