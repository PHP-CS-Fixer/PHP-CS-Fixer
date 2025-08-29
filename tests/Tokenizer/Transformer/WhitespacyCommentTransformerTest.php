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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @phpstan-import-type _PhpTokenArray from Token
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\WhitespacyCommentTransformer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class WhitespacyCommentTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param array<int, _PhpTokenArray> $expectedTokens
     *
     * @dataProvider provideProcessCases
     */
    public function testProcess(string $source, array $expectedTokens): void
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expectedTokens as $index => $expectedToken) {
            $token = $tokens[$index];

            self::assertSame($expectedToken[1], $token->getContent());
            self::assertSame($expectedToken[0], $token->getId());
        }
    }

    /**
     * @return iterable<int, array{string, array<int, _PhpTokenArray>}>
     */
    public static function provideProcessCases(): iterable
    {
        yield [
            "<?php // foo\n    \$a = 1;",
            [
                1 => [\T_COMMENT, '// foo'],
                2 => [\T_WHITESPACE, "\n    "],
            ],
        ];

        yield [
            "<?php // foo\n\n ",
            [
                1 => [\T_COMMENT, '// foo'],
                2 => [\T_WHITESPACE, "\n\n "],
            ],
        ];

        yield [
            "<?php // foo \r\n ",
            [
                1 => [\T_COMMENT, '// foo'],
                2 => [\T_WHITESPACE, " \r\n "],
            ],
        ];

        yield [
            '<?php /* foo1 */// foo2         ',
            [
                1 => [\T_COMMENT, '/* foo1 */'],
                2 => [\T_COMMENT, '// foo2'],
            ],
        ];
    }
}
