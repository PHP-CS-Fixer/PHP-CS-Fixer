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
use PhpCsFixer\Tokenizer\Transformer\PartialFunctionApplicationPlaceholderTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\PartialFunctionApplicationPlaceholderTransformer
 *
 * @phpstan-import-type _TransformerTestExpectedKindsUnderIndex from AbstractTransformerTestCase
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(PartialFunctionApplicationPlaceholderTransformer::class)]
final class PartialFunctionApplicationPlaceholderTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcessCases
     *
     * @requires PHP >= 8.6.0
     */
    #[DataProvider('provideProcessCases')]
    #[RequiresPhp('>= 8.6.0')]
    public function testProcess(array $expectedTokens, string $source): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                CT::T_NULLABLE_TYPE,
                CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
                '?',
            ],
        );
    }

    /**
     * @return iterable<string, array{_TransformerTestExpectedKindsUnderIndex, string}>
     */
    public static function provideProcessCases(): iterable
    {
        yield 'only placeholder' => [
            [
                3 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
            ],
            '<?php foo(?);',
        ];

        yield 'first, middle, and last placeholder' => [
            [
                3 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
                9 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
                15 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
            ],
            '<?php foo(?, 1, ?, 2, ?);',
        ];

        yield 'placeholder before variadic placeholder' => [
            [
                3 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
            ],
            '<?php foo(?, ...);',
        ];

        yield 'placeholder as named argument' => [
            [
                6 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
                12 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
            ],
            '<?php foo(x: ?, y: ?);',
        ];

        yield 'placeholder with comments and whitespace' => [
            [
                8 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
                14 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
            ],
            '<?php $obj->method( /* a */ ? /* b */ , ? );',
        ];

        yield 'placeholder in method and static method call' => [
            [
                5 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
                13 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
            ],
            '<?php $obj->foo(?); Foo::bar(?);',
        ];

        yield 'placeholder in ternary' => [
            [
                7 => '?',
                11 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
                18 => CT::T_PARTIAL_FUNCTION_APPLICATION_PLACEHOLDER,
            ],
            '<?php $f = $c ? foo(?) : bar(?);',
        ];

        yield 'nullable types and ternary are left alone' => [
            [
                5 => CT::T_NULLABLE_TYPE,
                11 => CT::T_NULLABLE_TYPE,
                23 => '?',
            ],
            '<?php function foo(?int $a, ?int $b) { return $a ? $b : 1; }',
        ];
    }
}
