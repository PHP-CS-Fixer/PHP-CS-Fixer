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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\ClassConstantTransformer
 */
final class ClassConstantTransformerTest extends AbstractTransformerTestCase
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
                CT::T_CLASS_CONSTANT,
            ]
        );
    }

    public static function provideProcessCases(): array
    {
        return [
            [
                '<?php echo X::class;',
                [
                    5 => CT::T_CLASS_CONSTANT,
                ],
            ],
            [
                '<?php echo X::cLaSS;',
                [
                    5 => CT::T_CLASS_CONSTANT,
                ],
            ],
            [
                '<?php echo X::bar;',
            ],
            [
                '<?php class X{}',
            ],
        ];
    }
}
