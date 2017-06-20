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

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\StarToSlashCommentFixer
 */
final class StarToSlashCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideDefaultCases
     */
    public function testDefaults($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideDefaultCases()
    {
        return [
            [
                '<?php
// lonely line
',
                '<?php
/* lonely line */
',
            ],
            [
                '<?php
   // indented line
',
                '<?php
   /* indented line */
',
            ],
            [
                '<?php
   // weird-spaced line
',
                '<?php
   /*   weird-spaced line*/
',
            ],
            [
                '<?php // start-end',
                '<?php /* start-end */',
            ],
            [
                "<?php\n \t \n \t // weird indent\n",
                "<?php\n \t \n \t /* weird indent */\n",
            ],
            [
                "<?php\n// with spaces after\n \t ",
                "<?php\n/* with spaces after */ \t \n \t ",
            ],
            [
                '<?php
$a = 1; // after code
',
                '<?php
$a = 1; /* after code */
',
            ],
            [
                '<?php
   /* first */ // second
',
                '<?php
   /* first */ /* second */
',
            ],
            [
                '<?php
    // one line',
                '<?php
    /*one line

     */',
            ],
            [
                '<?php
    // one line',
                '<?php
    /*

    one line*/',
            ],
            [
                '<?php
    // one line',
                "<?php
    /* \t "."
 \t   * one line ".'
     *
     */',
            ],
            [
                '<?php
//',
                '<?php
/***
 *
 */',
            ],
            [
                '<?php

    // s',
                '<?php

    /***
s    *
     */',
            ],

            // Untouched cases
            [
                '<?php
$a = 1; /* in code */ $b = 2;
',
            ],
            [
                '<?php
    /*
     * in code 2
     */ $a = 1;
',
            ],
            [
                '<?php
/***
 *
 */ $a = 1;',
            ],
            [
                '<?php
    /***
s    *
     */ $a = 1;',
            ],
            [
                '<?php
    /*
     * first line
     * second line
     */',
            ],
            [
                '<?php
    /*
     * first line
     *
     * second line
     */',
            ],
            [
                '<?php
    /*first line
second line*/',
            ],
            [
                '<?php /** inline doc comment */',
            ],
            [
                '<?php
    /**
     * Doc comment
     */',
            ],
        ];
    }
}
