<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

trait AssertTokensTrait
{
    public function assertTokens(Tokens $expectedTokens, Tokens $inputTokens)
    {
        foreach ($expectedTokens as $index => $expectedToken) {
            $this->assertTrue(isset($inputTokens[$index]), sprintf('Expected at index: %d token: "%s"', $index, $expectedToken->toJson()));
            $inputToken = $inputTokens[$index];

            $this->assertTrue(
                $expectedToken->equals($inputToken),
                sprintf('The token at index %d must be %s, got %s', $index, $expectedToken->toJson(), ($inputToken instanceof Token) ? $inputToken->toJson() : var_export($inputToken, true))
            );
        }

        $this->assertEquals($expectedTokens->count(), $inputTokens->count(), 'The collection must have the same length than the expected one.');

        $tokensReflection = new \ReflectionClass($expectedTokens);
        $propertyReflection = $tokensReflection->getProperty('foundTokenKinds');
        $propertyReflection->setAccessible(true);
        $foundTokenKinds = array_keys($propertyReflection->getValue($expectedTokens));

        foreach ($foundTokenKinds as $tokenKind) {
            $this->assertTrue(
                $inputTokens->isTokenKindFound($tokenKind),
                sprintf('The token kind %s must be found in fixed tokens collection.', $tokenKind)
            );
        }
    }
}
