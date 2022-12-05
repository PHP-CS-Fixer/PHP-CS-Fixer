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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer
 */
final class SingleLineCommentStyleFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        // @phpstan-ignore-next-line
        $this->fixer->configure(['abc']);
    }

    /**
     * @dataProvider provideAsteriskCases
     */
    public function testAsterisk(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['comment_types' => ['asterisk']]);
        $this->doTest($expected, $input);
    }

    public static function provideAsteriskCases(): array
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
   /* first */// second',
                '<?php
   /* first *//*
   second
   */',
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
            'empty comment' => [
                '<?php
//
',
                '<?php
/**/
',
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
            [
                '<?php # test',
            ],
        ];
    }

    /**
     * @dataProvider provideHashCases
     */
    public function testHashCases(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['comment_types' => ['hash']]);
        $this->doTest($expected, $input);
    }

    public static function provideHashCases(): array
    {
        return [
            [
                '<h1>This is an <?php //echo 123;?> example</h1>',
                '<h1>This is an <?php #echo 123;?> example</h1>',
            ],
            [
                '<?php
                    // test
                ',
                '<?php
                    # test
                ',
            ],
            [
                '<?php
                    // test1
                    //test2
                    // test3
                    // test 4
                ',
                '<?php
                    # test1
                    #test2
                    # test3
                    # test 4
                ',
            ],
            [
                '<?php //',
                '<?php #',
            ],
            // Untouched cases
            [
                '<?php
                    //#test
                ',
            ],
            [
                '<?php
                    /*
                        #test
                    */
                ',
            ],
            [
                '<?php // a',
                '<?php # a',
            ],
            [
                '<?php /* start-end */',
            ],
            [
                '<?php function foo(
    #[MyAttr([1, 2])] Type $myParam,
) {} // foo',
            ],
        ];
    }

    /**
     * @dataProvider provideAllCases
     */
    public function testAllCases(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideAllCases(): array
    {
        return [
            [
                '<?php
    // 1
    // 2
    /*
     * 3.a
     * 3.b
     */
    /**
     * 4
     */
    // 5
',
                '<?php
    /* 1 */
    /*
     * 2
     */
    /*
     * 3.a
     * 3.b
     */
    /**
     * 4
     */
    # 5
',
            ],
            [
                '<?php
                function foo() {
                    /* ?> */
                    return "bar";
                }',
            ],
        ];
    }
}
