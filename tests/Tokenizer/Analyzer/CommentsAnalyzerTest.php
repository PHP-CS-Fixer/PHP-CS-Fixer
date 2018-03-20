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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer
 */
final class CommentsAnalyzerTest extends TestCase
{
    public function testWhenNotPointingToComment()
    {
        $analyzer = new CommentsAnalyzer();
        $tokens = Tokens::fromCode('<?php $no; $comment; $here;');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given index must point to a comment.');

        $analyzer->getCommentBlockIndices($tokens, 4);
    }

    /**
     * @param string     $code
     * @param int        $index
     * @param null|array $borders
     *
     * @dataProvider provideCommentsCases
     */
    public function testComments($code, $index, $borders)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new CommentsAnalyzer();

        $this->assertSame($borders, $analyzer->getCommentBlockIndices($tokens, $index));
    }

    public function provideCommentsCases()
    {
        return [
            'discover all 4 comments for the 1st comment with slash' => [
                '<?php
$foo;
// one
// two
// three
// four
$bar;',
                4,
                [4, 6, 8, 10],
            ],
            'discover all 4 comments for the 1st comment with hash' => [
                '<?php
$foo;
# one
# two
# three
# four
$bar;',
                4,
                [4, 6, 8, 10],
            ],
            'discover 3 comments out of 4 for the 2nd comment' => [
                '<?php
$foo;
// one
// two
// three
// four
$bar;',
                6,
                [6, 8, 10],
            ],
            'discover 3 comments when empty line separates 4th' => [
                '<?php
$foo;
// one
// two
// three

// four
$bar;',
                4,
                [4, 6, 8],
            ],
            'discover 3 comments when empty line of CR separates 4th' => [
                str_replace("\n", "\r", '<?php
$foo;
// one
// two
// three

// four
$bar;'),
                4,
                [4, 6, 8],
            ],
            'discover correctly when mix of slash and hash' => [
                '<?php
$foo;
// one
// two
# three
// four
$bar;',
                4,
                [4, 6],
            ],
            'do not group asterisk comments' => [
                '<?php
$foo;
/* one */
/* two */
/* three */
$bar;',
                4,
                [4],
            ],
            'handle fancy indent' => [
                '<?php
$foo;
        // one
       //  two
      //   three
     //    four
$bar;',
                4,
                [4, 6, 8, 10],
            ],
        ];
    }
}
