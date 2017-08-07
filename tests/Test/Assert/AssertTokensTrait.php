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

use PhpCsFixer\AccessibleObject\AccessibleObject;
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
        foreach ($expectedTokens as $index => $expectedToken) {
            $inputToken = $inputTokens[$index];
            $option = ['JSON_PRETTY_PRINT'];
            $this->assertTrue(
                $expectedToken->equals($inputToken),
                sprintf("The token at index %d must be:\n%s,\ngot:\n%s.", $index, $expectedToken->toJson($option), $inputToken->toJson($option))
            );
        }

        $this->assertSame($expectedTokens->count(), $inputTokens->count(), 'The collection must have the same length than the expected one.');

        $foundTokenKinds = array_keys(AccessibleObject::create($expectedTokens)->foundTokenKinds);

        foreach ($foundTokenKinds as $tokenKind) {
            $this->assertTrue(
                $inputTokens->isTokenKindFound($tokenKind),
                sprintf('The token kind %s must be found in fixed tokens collection.', $tokenKind)
            );
        }
    }
}
