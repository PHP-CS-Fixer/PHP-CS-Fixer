<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class WhitespacyCommentTransformerTest extends AbstractTransformerTestCase
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
                "<?php // foo\n    \$a = 1;",
                array(
                    1 => array(T_COMMENT, '// foo'),
                    2 => array(T_WHITESPACE, "\n    "),
                ),
            ),
            array(
                "<?php // foo\n\n ",
                array(
                    1 => array(T_COMMENT, '// foo'),
                    2 => array(T_WHITESPACE, "\n\n "),
                ),
            ),
            array(
                "<?php // foo \r\n ",
                array(
                    1 => array(T_COMMENT, '// foo'),
                    2 => array(T_WHITESPACE, " \r\n "),
                ),
            ),
        );
    }
}
