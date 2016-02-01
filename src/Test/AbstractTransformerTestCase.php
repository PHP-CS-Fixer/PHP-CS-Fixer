<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Test;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractTransformerTestCase extends \PHPUnit_Framework_TestCase
{
    protected function doTest($source, array $expectedTokens = array())
    {
        $tokens = Tokens::fromCode($source);

        $this->assertSame(
            count($expectedTokens),
            array_sum(array_map(
                function ($item) { return count($item); },
                $tokens->findGivenKind(array_map(function ($name) { return constant($name); }, $expectedTokens))
            ))
        );

        foreach ($expectedTokens as $index => $name) {
            $this->assertSame($name, $tokens[$index]->getName());
            $this->assertSame(constant($name), $tokens[$index]->getId());
        }
    }
}
