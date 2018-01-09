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
    protected function doTest($source, array $expectedTokens = [], array $observedKindsOrPrototypes = [])
    {
        $tokens = Tokens::fromCode($source);

        $this->assertSame(
            count($expectedTokens),
            $this->countTokenPrototypes(
                $tokens,
                array_map(
                    static function ($kindOrPrototype) {
                        return is_int($kindOrPrototype) ? [$kindOrPrototype] : $kindOrPrototype;
                    },
                    array_unique(array_merge($observedKindsOrPrototypes, $expectedTokens))
                )
            ),
            'Number of expected tokens does not match actual token count.'
        );

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

    /**
     * @param Tokens $tokens
     * @param array  $prototypes
     *
     * @return int
     */
    private function countTokenPrototypes(Tokens $tokens, array $prototypes)
    {
        $count = 0;

        foreach ($tokens as $token) {
            if ($token->equalsAny($prototypes)) {
                ++$count;
            }
        }

        return $count;
    }
}
