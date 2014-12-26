<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer\Transformer;

use Symfony\CS\Tests\Tokenizer\AbstractTransformerTestBase;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class WhitespacyCommentTransformerTest extends AbstractTransformerTestBase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens)
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expectedTokens as $index => $expectedToken) {
            $token = $tokens[$index];

            $this->assertSame($expectedToken[1], $token->getContent());
            $this->assertSame($expectedToken[0], $token->getId());
        }
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php
    // foo
    $a = 1;',
                array(
                    2 => array(T_COMMENT, '// foo'),
                    3 => array(T_WHITESPACE, "\n    "),
                ),
            ),
            array(
                '<?php
    // foo
',
                array(
                    2 => array(T_COMMENT, '// foo'),
                    3 => array(T_WHITESPACE, "\n"),
                ),
            ),
        );
    }
}
