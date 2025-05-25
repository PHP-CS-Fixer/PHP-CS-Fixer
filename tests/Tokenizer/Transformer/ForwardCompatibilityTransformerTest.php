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
 *
 * @requires PHP 8.0
 */
final class ForwardCompatibilityTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param _TransformerTestExpectedTokens $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $this->doTest($source, $expectedTokens, (new ForwardCompatibilityTransformer())->getCustomTokens());
    }

    /**
     * @return iterable<string, array{string, _TransformerTestExpectedTokens}>
     */
    public static function provideProcessCases(): iterable
    {
        yield 'T_MATCH' => [
            <<<'PHP'
                <?php
                return match (0) { 1 => 2 };
                PHP,
            [
                3 => CT::T_MATCH,
            ],
        ];
    }
}
