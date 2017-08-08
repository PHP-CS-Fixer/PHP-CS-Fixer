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

namespace PhpCsFixer\Tests\Test\Assert;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
trait AssertTokensTrait
{
    private function assertTokens(Tokens $expectedTokens, Tokens $inputTokens)
    {
        $option = ['JSON_PRETTY_PRINT'];

        foreach ($expectedTokens as $index => $expectedToken) {
            $inputToken = $inputTokens[$index];

            $this->assertTrue(
                $expectedToken->equals($inputToken),
                sprintf("The token at index %d must be:\n%s,\ngot:\n%s.", $index, $expectedToken->toJson($option), $inputToken->toJson($option))
            );

            $expectedTokenKind = $expectedToken->isArray() ? $expectedToken->getId() : $expectedToken->getContent();
            $this->assertTrue(
                $inputTokens->isTokenKindFound($expectedTokenKind),
                sprintf('The token kind %s must be found in fixed tokens collection.', $expectedTokenKind)
            );
        }

        $this->assertSame($expectedTokens->count(), $inputTokens->count(), 'Both collections must have the same length.');
    }
}
