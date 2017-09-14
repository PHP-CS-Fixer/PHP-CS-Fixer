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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractTransformerTestCase extends TestCase
{
    protected function doTest($source, array $expectedTokens = array(), array $observedKinds = array())
    {
        $tokens = Tokens::fromCode($source);

        if (count($observedKinds)) {
            $observedKinds = array_unique(array_merge(
                $observedKinds,
                array_filter($expectedTokens, function ($item) {
                    return !is_string($item);
                })
            ));

            $this->assertSame(
                count($expectedTokens),
                array_sum(array_map(
                    function ($item) {
                        return count($item);
                    },
                    $tokens->findGivenKind($observedKinds)
                )),
                'Number of expected tokens does not match actual token count.'
            );
        }

        foreach ($expectedTokens as $index => $tokenIdOrContent) {
            if (is_string($tokenIdOrContent)) {
                $this->assertTrue($tokens[$index]->equals($tokenIdOrContent));

                continue;
            }

            $this->assertSame(
                CT::has($tokenIdOrContent) ? CT::getName($tokenIdOrContent) : token_name($tokenIdOrContent),
                $tokens[$index]->getName(),
                sprintf('Token name should be the same. Got token "%s" at index %d.', $tokens[$index]->toJson(), $index)
            );

            $this->assertSame(
                $tokenIdOrContent,
                $tokens[$index]->getId(),
                sprintf('Token id should be the same. Got token "%s" at index %d.', $tokens[$index]->toJson(), $index)
            );
        }
    }
}
