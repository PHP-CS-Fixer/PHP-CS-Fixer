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

use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer>
 */
final class NoEmptyCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        // fix cases
        yield [
            '<?php
                    echo 0;
echo 1;
                ',
            '<?php
                    echo 0;//
echo 1;
                ',
        ];

        yield [
            '<?php
                    echo 0;
    echo 1;
                ',
            '<?php
                    echo 0;//
    echo 1;
                ',
        ];

        yield [
            '<?php
                    echo 1;
                ',
            '<?php
                    echo 1;//
                ',
        ];

        yield [
            '<?php
                echo 2;
                    '.'
echo 1;
                ',
            '<?php
                echo 2;
                    //
echo 1;
                ',
        ];

        yield [
            '<?php

                ?>',
            '<?php

                //?>',
        ];

        yield [
            '<?php
                    '.'
                ',
            '<?php
                    //
                ',
        ];

        yield [
            '<?php
                    '.'
                ',
            '<?php
                    #
                ',
        ];

        yield [
            '<?php
                    '.'
                ',
            '<?php
                    /**/
                ',
        ];

        yield [
            '<?php
                    echo 0;echo 1;
                ',
            '<?php
                    echo 0;/**/echo 1;
                ',
        ];

        yield [
            '<?php
                    echo 0;echo 1;
                ',
            '<?php
                    echo 0;/**//**//**/echo 1/**/;
                ',
        ];

        yield [
            '<?php
                ',
            '<?php
                //',
        ];

        yield [
            '<?php
                ',
            '<?php
                /*


                */',
        ];

        yield [
            "<?php\n                    \n                    \n                    \n                    \n                ",
            "<?php\n                    //\n                    //\n                    //\n                    /**///\n                ",
        ];

        yield [
            "<?php\r                    \r                    \r                    \r                    \r                ",
            "<?php\r                    //\r                    //\r                    //\r                    /**///\r                ",
        ];

        yield [
            "<?php\r\n                    \r\n                    \r\n                    \r\n                    \r\n                ",
            "<?php\r\n                    //\r\n                    //\r\n                    //\r\n                    /**///\r\n                ",
        ];

        yield [
            "<?php\necho 1;\r\recho 2;",
            "<?php\necho 1;\r//\recho 2;",
        ];

        // do not fix cases
        yield [
            '<?php
                // a
            // /**/
              // #
/* b */ // s
          #                        c',
        ];

        yield [
            '<?php
                    // This comment could be nicely formatted.
                    //
                    //
                    // For that, it could have some empty comment lines inside.
                    //

                    ## A 1
                    ##
                    ##
                    ## A 2
                    ##

                    // B 1
                    //
                    // B 2

                    ## C 1
                    ##
                    ## C 2

                    $foo = 1;

                    //
                    // a
                    //

                    $bar = 2;
                ',
        ];

        yield [
            '<?php
                    '.'
                ',
            '<?php
                    /*
                     *
                     */
                ',
        ];

        yield [
            '<?php
                    '.'
                ',
            '<?php
                    /********
                     *
                     ********/
                ',
        ];

        yield [
            '<?php /* a */',
            '<?php /* *//* a *//* */',
        ];

        yield [
            '<?php
                    '.'
                    /* a */
                    '.'
                ',
            '<?php
                    //
                    /* a */
                    //
                ',
        ];
    }

    /**
     * @param string $source     valid PHP source code
     * @param int    $startIndex start index of the comment block
     * @param int    $endIndex   expected index of the last token of the block
     * @param bool   $isEmpty    expected value of empty flag returned
     *
     * @dataProvider provideGetCommentBlockCases
     */
    public function testGetCommentBlock(string $source, int $startIndex, int $endIndex, bool $isEmpty): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        self::assertTrue($tokens[$startIndex]->isComment(), \sprintf('Misconfiguration of test, expected comment token at index "%d".', $startIndex));

        $foundInfo = \Closure::bind(static fn (NoEmptyCommentFixer $fixer): array => $fixer->getCommentBlock($tokens, $startIndex), null, NoEmptyCommentFixer::class)($this->fixer);

        self::assertSame($startIndex, $foundInfo['blockStart'], 'Find start index of block failed.');
        self::assertSame($endIndex, $foundInfo['blockEnd'], 'Find end index of block failed.');
        self::assertSame($isEmpty, $foundInfo['isEmpty'], 'Is empty comment block detection failed.');
    }

    /**
     * @return iterable<int, array{string, int, int, bool}>
     */
    public static function provideGetCommentBlockCases(): iterable
    {
        yield [
            '<?php // a',
            1,
            1,
            false,
        ];

        yield [
            '<?php
                    // This comment could be nicely formatted.
                    //
                    //
                    // For that, it could have some empty comment lines inside.
                    //           ',
            2,
            11,
            false,
        ];

        yield [
            '<?php
/**///',
            1,
            1,
            true,
        ];

        yield [
            '<?php
//
//

#
#
',
            5,
            8,
            true,
        ];

        yield [
            '<?php
//
//

//
//
',
            5,
            8,
            true,
        ];

        yield [
            '<?php
//
//

//
//
',
            1,
            3,
            true,
        ];

        yield [
            str_replace("\n", "\r", "<?php\n//\n//\n\n//\n//\n"),
            1,
            3,
            true,
        ];

        yield [
            str_replace("\n", "\r\n", "<?php\n//\n//\n\n//\n//\n"),
            1,
            3,
            true,
        ];

        yield [
            '<?php
//

//
',
            1,
            1,
            true,
        ];

        yield [
            '<?php
//
//
              $a;  ',
            1,
            4,
            true,
        ];

        yield [
            '<?php
//',
            1,
            1,
            true,
        ];

        $src = '<?php
                // a2
            // /*4*/
              // #6
/* b8 */ // s10
          #                        c12';

        foreach ([2, 4, 6] as $i) {
            yield [$src, $i, 7, false];
        }

        yield [$src, 8, 8, false];

        yield [$src, 10, 11, false];

        yield [$src, 12, 12, false];
    }
}
