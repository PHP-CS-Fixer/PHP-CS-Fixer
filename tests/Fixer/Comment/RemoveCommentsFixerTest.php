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

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Fixer\Comment\RemoveCommentsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\RemoveCommentsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Comment\RemoveCommentsFixer>
 *
 * @author Your name <your@email.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(RemoveCommentsFixer::class)]
final class RemoveCommentsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'comments without a preceding semicolon are kept' => [
            <<<'PHP'
                <?php

                // comment
                $hoge = 'hogehoge';

                PHP,
        ];

        yield 'comments with a preceding semicolon are removed' => [
            "<?php \$piyo = 'piyopiyo'; ",
            "<?php \$piyo = 'piyopiyo'; /* comment */",
        ];
    }
}
