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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer
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

    public static function provideFixCases(): array
    {
        return [
            // fix cases
            [
                '<?php
                    echo 0;
echo 1;
                ',
                '<?php
                    echo 0;//
echo 1;
                ',
            ],
            [
                '<?php
                    echo 0;
    echo 1;
                ',
                '<?php
                    echo 0;//
    echo 1;
                ',
            ],
            [
                '<?php
                    echo 1;
                ',
                '<?php
                    echo 1;//
                ',
            ],
            [
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
            ],
            [
                '<?php

                ?>',
                '<?php

                //?>',
            ],
            [
                '<?php
                    '.'
                ',
                '<?php
                    //
                ',
            ],
            [
                '<?php
                    '.'
                ',
                '<?php
                    #
                ',
            ],
            [
                '<?php
                    '.'
                ',
                '<?php
                    /**/
                ',
            ],
            [
                '<?php
                    echo 0;echo 1;
                ',
                '<?php
                    echo 0;/**/echo 1;
                ',
            ],
            [
                '<?php
                    echo 0;echo 1;
                ',
                '<?php
                    echo 0;/**//**//**/echo 1/**/;
                ',
            ],
            [
                '<?php
                ',
                '<?php
                //',
            ],
            [
                '<?php
                ',
                '<?php
                /*


                */',
            ],
            [
                '<?php
                    '.'
                    '.'
                    '.'
                    '.'
                ',
                '<?php
                    //
                    //
                    //
                    /**///
                ',
            ],
            [
                "<?php\n                    \n                    \n                    \n                    \n                ",
                "<?php\n                    //\n                    //\n                    //\n                    /**///\n                ",
            ],
            [
                "<?php\r                    \r                    \r                    \r                    \r                ",
                "<?php\r                    //\r                    //\r                    //\r                    /**///\r                ",
            ],
            [
                "<?php\r\n                    \r\n                    \r\n                    \r\n                    \r\n                ",
                "<?php\r\n                    //\r\n                    //\r\n                    //\r\n                    /**///\r\n                ",
            ],
            [
                "<?php\necho 1;\r\recho 2;",
                "<?php\necho 1;\r//\recho 2;",
            ],
            // do not fix cases
            [
                '<?php
                // a
            // /**/
              // #
/* b */ // s
          #                        c',
            ],
            [
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
            ],
            [
                '<?php
                    '.'
                ',
                '<?php
                    /*
                     *
                     */
                ',
            ],
            [
                '<?php
                    '.'
                ',
                '<?php
                    /********
                     *
                     ********/
                ',
            ],
            [
                '<?php /* a */',
                '<?php /* *//* a *//* */',
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string $source     valid PHP source code
     * @param int    $startIndex start index of the comment block
     * @param int    $endIndex   expected index of the last token of the block
     * @param bool   $isEmpty    expected value of empty flag returned
     *
     * @dataProvider provideCommentBlockCases
     */
    public function testGetCommentBlock(string $source, int $startIndex, int $endIndex, bool $isEmpty): void
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($source);
        static::assertTrue($tokens[$startIndex]->isComment(), sprintf('Misconfiguration of test, expected comment token at index "%d".', $startIndex));

        $method = new \ReflectionMethod($this->fixer, 'getCommentBlock');
        $method->setAccessible(true);

        [$foundStart, $foundEnd, $foundIsEmpty] = $method->invoke($this->fixer, $tokens, $startIndex);

        static::assertSame($startIndex, $foundStart, 'Find start index of block failed.');
        static::assertSame($endIndex, $foundEnd, 'Find end index of block failed.');
        static::assertSame($isEmpty, $foundIsEmpty, 'Is empty comment block detection failed.');
    }

    public static function provideCommentBlockCases(): array
    {
        $cases = [
            [
                '<?php // a',
                1,
                1,
                false,
            ],
            [
                '<?php
                    // This comment could be nicely formatted.
                    //
                    //
                    // For that, it could have some empty comment lines inside.
                    //           ',
                2,
                11,
                false,
            ],
            [
                '<?php
/**///',
                1,
                1,
                true,
            ],
            [
                '<?php
//
//

#
#
',
                5,
                8,
                true,
            ],
            [
                '<?php
//
//

//
//
',
                5,
                8,
                true,
            ],
            [
                '<?php
//
//

//
//
',
                1,
                3,
                true,
            ],
            [
                str_replace("\n", "\r", "<?php\n//\n//\n\n//\n//\n"),
                1,
                3,
                true,
            ],
            [
                str_replace("\n", "\r\n", "<?php\n//\n//\n\n//\n//\n"),
                1,
                3,
                true,
            ],
            [
                '<?php
//

//
',
                1,
                1,
                true,
            ],
            [
                '<?php
//
//
              $a;  ',
                1,
                4,
                true,
            ],
            [
                '<?php
//',
                1,
                1,
                true,
            ],
        ];

        $src = '<?php
                // a2
            // /*4*/
              // #6
/* b8 */ // s10
          #                        c12';

        foreach ([2, 4, 6] as $i) {
            $cases[] = [$src, $i, 7, false];
        }

        $cases[] = [$src, 8, 8, false];
        $cases[] = [$src, 10, 11, false];
        $cases[] = [$src, 12, 12, false];

        return $cases;
    }
}
