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

namespace PhpCsFixer\Tests\Tokenizer\Manipulator;

use PhpCsFixer\Tests\Test\Assert\AssertTokensTrait;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Manipulator\TokenRemover;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Manipulator\TokenRemover
 */
final class TokenRemoverTest extends TestCase
{
    use AssertTokensTrait;

    /**
     * @param int[]  $indexes  token index to clear
     * @param string $expected PHP source expected after clearing
     * @param string $input    PHP source input
     *
     * @dataProvider provideClearTokenCases
     */
    public function testClearToken(array $indexes, $expected, $input)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($input);
        $expectedTokens = Tokens::fromCode($expected);

        $remover = new TokenRemover($tokens);

        foreach ($indexes as $index) {
            $remover->clearToken($index);
        }

        $tokens->clearEmptyTokens();
        static::assertSame($expectedTokens->generateCode(), $tokens->generateCode());
        static::assertTokens($expectedTokens, $tokens);
    }

    public function provideClearTokenCases()
    {
        $cases = [
            [
                [2],
                '<?php
/* other comment
 */
',
                '<?php
/* other comment
 *//* to remove */
',
            ],
            [
                [1],
                '<?php
/* */',
                '<?php
/* remove */ '.'
/* */',
            ],
            [
                [6],
                '<?php
                    echo 12;
                ',
                '<?php
                    echo 12;//
                ',
            ],
            [
                [2],
                '<?php ',
                '<?php
   // 1100',
            ],
            '1011/' => [
                [2],
                '<?php ',
                '<?php
   /* 1011 */  ',
            ],
            '1010/' => [
                [2],
                '<?php
',
                '<?php
   /* 1010 */
',
            ],
            '1013/' => [
                [2],
                '<?php
 echo 1;',
                '<?php
   /* 1013 */
 echo 1;',
            ],
            [
                [1],
                '<?php
 echo 100;
',
                '<?php
/* */      '.'
 echo 100;
',
            ],
            [
                [1],
                '<?php ',
                '<?php
/* */   ',
            ],
            [
                [1],
                '<?php
',
                '<?php
/* */   '.'
',
            ],
            [
                [1],
                '<?php   echo 1;',
                '<?php /* */  echo 1;',
            ],
            [
                [1],
                '<?php
 echo 1;',
                '<?php
/* */ echo 1;',
            ],
            [
                [1],
                '<?php
echo 1;',
                '<?php
/* */echo 1;',
            ],
            [
                [1],
                '<?php ',
                '<?php
/* */',
            ],
            [
                [0],
                '',
                '<?php',
            ],
            [
                [7],
                '<?php
                    echo 2;
                      echo 122;
                ',
                '<?php
                    echo 2;
                    /* */  echo 122;
                ',
            ],
            [
                [7],
                '<?php
                    echo 27;
                    echo 1;
                ',
                '<?php
                    echo 27;
                    /* */echo 1;
                ',
            ],
            [
                [5],
                '<?php echo 18;
echo 28;',
                '<?php echo 18;/* */
echo 28;',
            ],
            [
                [6],
                '<?php echo 19;    '.'
echo 29;',
                '<?php echo 19;  /* */  '.'
echo 29;',
            ],
            [
                [6],
                '<?php
echo 11;    '.'
echo 21;
                ',
                '<?php
echo 11;    '.'
# 1
echo 21;
                ',
            ],
            [
                [6],
                '<?php
echo 71;
    '.'
  ',
                '<?php
echo 71;
        /* 1 */   '.'
    '.'
  ',
            ],
            [
                [6],
                '<?php
echo 81;',
                '<?php
echo 81;
        /* 1 */',
            ],
            [
                [6],
                '<?php
echo 91;',
                '<?php
echo 91;
        /* 1 */                              ',
            ],
            [
                [6],
                '<?php
echo 61;
echo 62;
                ',
                '<?php
echo 61;
        # 1
echo 62;
                ',
            ],
            [
                [6],
                '<?php
echo 511;
  echo 512;
                ',
                '<?php
echo 511;
# 1
  echo 512;
                ',
            ],
            [
                [6],
                '<?php
echo 51;
echo 52;
                ',
                '<?php
echo 51;
# 1
echo 52;
                ',
            ],
            [
                [4],
                '<?php

 # A8
 # B
                ',
                '<?php

 # A8
    ;    '.'
 # B
                ',
            ],
            [
                [1],
                '<?php
',
                '<?php
;
',
            ],
            [
                [2],
                '<?php ',
                "<?php\n         ;",
            ],
            [
                [1],
                '<?php
  '.'
',
                '<?php
;
  '.'
',
            ],
            [
                [3],
                '<?php # 0',
                '<?php # 0
;',
            ],
            [
                [3],
                '<?php
/* 1 */
  ',
                '<?php
/* 1 */
;
  ',
            ],
            [
                [3],
                '<?php
/* 1 */
  # 1.2',
                '<?php
/* 1 */
;
  # 1.2',
            ],
            [
                [4],
                '<?php
                /* 2 */  '.'
                ',
                '<?php
                /* 2 */  ;
                ',
            ],
            [
                [3],
                '<?php # 3
    ',
                '<?php # 3
    '.'
                ;',
            ],
            [
                [2],
                '<?php            ',
                '<?php            ;',
            ],
            '5/' => [
                [1],
                '<?php ',
                "<?php\n;",
            ],
            [
                [3],
                '<?php
# A7
# B7
',
                '<?php
# A7
;
# B7
',
            ],
            [
                [4],
                '<?php
                # A9
                # B
                ',
                '<?php
                # A9
                ;        '.'
                # B
                ',
            ],
            [
                [1],
                '<?php ',
                '<?php
;     ',
            ],
            [
                [2],
                '<?php
',
                '<?php

;     ',
            ],
            [
                [1],
                '<?php ',
                '<?php
;',
            ],
            [
                [3],
                '<?php
# a',
                '<?php
# a
;     ',
            ],
            '16/' => [
                [1],
                '<?php
 # 1
',
                '<?php
; # 1
',
            ],
            [
                [3],
                '<?php
# 17a
 # 18a
',
                '<?php
# 17a
; # 18a
',
            ],
            [
                [3],
                '<?php
# 17b
  # 18b
',
                '<?php
# 17b
 ; # 18b
',
            ],
            [
                [3],
                '<?php
# 17c
 # 18c
',
                '<?php
# 17c
 ;# 18c
',
            ],
            [
                [2],
                '<?php
/* 17d*/# 18d
',
                '<?php
/* 17d*/;# 18d
',
            ],
            [
                [0],
                '',
                '<?php ',
            ],
            [
                [1],
                '<?php ',
                '<?php  ',
            ],
            [
                [1],
                '<?php ',
                "<?php\n ",
            ],
            [
                [2],
                '<?php
 '.'
  '.'
   '.'
    '.'
     '.'
     '.'
    '.'
   '.'
  '.'
 ',
                '<?php
 '.'
  '.'
   '.'
    '.'
     '.'
    # delete this line only 22
     '.'
    '.'
   '.'
  '.'
 ',
            ],
            [
                [1],
                '<?php ',
                '<?php
;',
            ],
            [
                [1],
                '<?php
# 1',
                '<?php
;# 1',
            ],
            [
                [1],
                '<?php
 ;',
                '<?php
; ;',
            ],
            [
                [1],
                '<?php
   #1',
                '<?php
;
   #1',
            ],
            [
                [1],
                '<?php
#2',
                '<?php
;
#2',
            ],
            [
                [1],
                '<?php
#3',
                '<?php
;#3',
            ],
            'A' => [
                [2, 3, 4, 5],
                '<?php
                ',
                '<?php
                    /** A *//** B *//** C *//** D */
                ',
            ],
            'B' => [
                [3, 4, 5, 6, 7, 8, 10, 12, 14, 15, 16, 18, 20, 21, 22],
                '<?php
// 1
// 2',
                '<?php
// 1
/** A0 *//** B0 *//** C0 *//** D0 */
                /** A1 */ /** B1 */ /** C1 */ /** D1 */
            /** A2 */   /** B2 */     /** C2 */
        /** D2 */
// 2',
            ],
        ];

        foreach ($cases  as $label => $test) {
            yield $label => $test;

            $test[0] = array_reverse($test[0]);

            yield $label.' reversed' => $test;
        }
    }

    /**
     * @param int    $index    token index to clear
     * @param string $expected PHP source expected after clearing
     * @param string $input    PHP source input
     *
     * @dataProvider provideClearToken2Cases
     */
    public function testClearToken2($index, $expected, $input)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($input);

        $remover = new TokenRemover($tokens);
        $remover->clearToken($index);

        $tokens->clearEmptyTokens();

        static::assertSame($expected, $tokens->generateCode());
    }

    public function provideClearToken2Cases()
    {
        return [
            'A1' => [
                0,
                '',
                '<?php ',
            ],
            'B1' => [
                0,
                '',
                '<?php    ',
            ],
            'C1' => [
                0,
                '  ',
                "<?php    \n  ",
            ],
            'C2' => [
                0,
                '',
                "<?php    \n",
            ],
            'D1' => [
                0,
                'echo 1;',
                "<?php\necho 1;",
            ],
            'D2' => [
                0,
                ' echo 12;',
                "<?php\n echo 12;",
            ],
            'D3' => [
                0,
                " \necho 14;",
                "<?php\n \necho 14;",
            ],
        ];
    }
}
