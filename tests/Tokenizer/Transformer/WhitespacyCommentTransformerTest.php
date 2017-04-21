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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\WhitespacyCommentTransformer
 */
final class WhitespacyCommentTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
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
        return [
            [
                "<?php // foo\n    \$a = 1;",
                [
                    1 => [T_COMMENT, '// foo'],
                    2 => [T_WHITESPACE, "\n    "],
                ],
            ],
            [
                "<?php // foo\n\n ",
                [
                    1 => [T_COMMENT, '// foo'],
                    2 => [T_WHITESPACE, "\n\n "],
                ],
            ],
            [
                "<?php // foo \r\n ",
                [
                    1 => [T_COMMENT, '// foo'],
                    2 => [T_WHITESPACE, " \r\n "],
                ],
            ],
        ];
    }
}
