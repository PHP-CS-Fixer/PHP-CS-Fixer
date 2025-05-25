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
use PhpCsFixer\Tokenizer\Transformer\ForwardCompatibilityTransformer;

/**
 * @covers \PhpCsFixer\Tokenizer\Transformer\ForwardCompatibilityTransformer
 *
 * @internal
 *
 * @phpstan-import-type _TransformerTestExpectedTokens from AbstractTransformerTestCase
 */
final class ForwardCompatibilityTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedTokens $expectedTokens
     *
     * @dataProvider provideProcess84Cases
     *
     * @requires PHP 8.4
     */
    public function testProcess84(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens, (new ForwardCompatibilityTransformer())->getCustomTokens());
    }

    /**
     * @return iterable<string, array{string, _TransformerTestExpectedTokens}>
     */
    public static function provideProcess84Cases(): iterable
    {
        yield 'T_*_SET' => [
            <<<'PHP'
                <?php class Foo
                {
                    public public(set) bool $a;
                    public protected(set) bool $b;
                    public private(set) bool $c;
                }
                PHP,
            [
                9 => CT::T_PUBLIC_SET,
                18 => CT::T_PROTECTED_SET,
                27 => CT::T_PRIVATE_SET,
            ],
        ];
    }
}
