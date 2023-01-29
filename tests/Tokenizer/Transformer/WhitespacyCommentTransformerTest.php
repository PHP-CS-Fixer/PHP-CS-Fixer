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
     * @param array<int, array{int, string}> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expectedTokens as $index => $expectedToken) {
            $token = $tokens[$index];

            static::assertSame($expectedToken[1], $token->getContent());
            static::assertSame($expectedToken[0], $token->getId());
        }
    }

    public static function provideProcessCases(): array
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
            [
                '<?php /* foo1 */// foo2         ',
                [
                    1 => [T_COMMENT, '/* foo1 */'],
                    2 => [T_COMMENT, '// foo2'],
                ],
            ],
        ];
    }
}
