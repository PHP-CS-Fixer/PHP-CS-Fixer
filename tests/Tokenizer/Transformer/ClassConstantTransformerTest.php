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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @phpstan-import-type _TransformerTestExpectedKindsUnderIndex from AbstractTransformerTestCase
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\ClassConstantTransformer
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ClassConstantTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
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

    /**
     * @return iterable<int, array{0: string, 1?: _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcessCases(): iterable
    {
        yield [
            '<?php echo X::class;',
            [
                5 => CT::T_CLASS_CONSTANT,
            ],
        ];

        yield [
            '<?php echo X::cLaSS;',
            [
                5 => CT::T_CLASS_CONSTANT,
            ],
        ];

        yield [
            '<?php echo X::bar;',
        ];

        yield [
            '<?php class X{}',
        ];
    }
}
