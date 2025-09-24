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
 * @covers \PhpCsFixer\Tokenizer\Transformer\NamespaceOperatorTransformer
 *
 * @author Gregor Harlan <gharlan@web.de>
 */
final class NamespaceOperatorTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedKindsUnderIndex $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest(
            $source,
            $expectedTokens,
            [
                \T_NAMESPACE,
                CT::T_NAMESPACE_OPERATOR,
            ]
        );
    }

    /**
     * @return iterable<int, array{string, _TransformerTestExpectedKindsUnderIndex}>
     */
    public static function provideProcessCases(): iterable
    {
        yield [
            '<?php
namespace Foo;
namespace\Bar\baz();
',
            [
                1 => \T_NAMESPACE,
                6 => CT::T_NAMESPACE_OPERATOR,
            ],
        ];
    }
}
