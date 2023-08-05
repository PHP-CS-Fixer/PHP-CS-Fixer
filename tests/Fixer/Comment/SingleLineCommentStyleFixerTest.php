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

    public static function provideAsteriskCases(): iterable
    {
        yield [
            '<?php
// lonely line
',
            '<?php
/* lonely line */
',
        ];

        yield [
            '<?php
   // indented line
',
            '<?php
   /* indented line */
',
        ];

        yield [
            '<?php
   // weird-spaced line
',
            '<?php
   /*   weird-spaced line*/
',
        ];

        yield [
            '<?php // start-end',
            '<?php /* start-end */',
        ];

        yield [
            "<?php\n \t \n \t // weird indent\n",
            "<?php\n \t \n \t /* weird indent */\n",
        ];

        yield [
            "<?php\n// with spaces after\n \t ",
            "<?php\n/* with spaces after */ \t \n \t ",
        ];

        yield [
            '<?php
$a = 1; // after code
',
            '<?php
$a = 1; /* after code */
',
        ];

        yield [
            '<?php
   /* first */ // second
',
            '<?php
   /* first */ /* second */
',
        ];

        yield [
            '<?php
   /* first */// second',
            '<?php
   /* first *//*
   second
   */',
        ];

        yield [
            '<?php
    // one line',
            '<?php
    /*one line

     */',
        ];

        yield [
            '<?php
    // one line',
            '<?php
    /*

    one line*/',
        ];

        yield [
            '<?php
    // one line',
            "<?php
    /* \t "."
 \t   * one line ".'
     *
     */',
        ];

        yield [
            '<?php
//',
            '<?php
/***
 *
 */',
        ];

        yield [
            '<?php

    // s',
            '<?php

    /***
s    *
     */',
        ];

        yield 'empty comment' => [
            '<?php
//
',
            '<?php
/**/
',
        ];

        // Untouched cases
        yield [
            '<?php
$a = 1; /* in code */ $b = 2;
',
        ];

        yield [
            '<?php
    /*
     * in code 2
     */ $a = 1;
',
        ];

        yield [
            '<?php
/***
 *
 */ $a = 1;',
        ];

        yield [
            '<?php
    /***
s    *
     */ $a = 1;',
        ];

        yield [
            '<?php
    /*
     * first line
     * second line
     */',
        ];

        yield [
            '<?php
    /*
     * first line
     *
     * second line
     */',
        ];

        yield [
            '<?php
    /*first line
second line*/',
        ];

        yield [
            '<?php /** inline doc comment */',
        ];

        yield [
            '<?php
    /**
     * Doc comment
     */',
        ];

        yield [
            '<?php # test',
        ];
    }

    /**
     * @dataProvider provideHashCases
     */
    public function testHash(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['comment_types' => ['hash']]);
        $this->doTest($expected, $input);
    }

    public static function provideHashCases(): iterable
    {
        yield [
            '<h1>This is an <?php //echo 123;?> example</h1>',
            '<h1>This is an <?php #echo 123;?> example</h1>',
        ];

        yield [
            '<?php
                    // test
                ',
            '<?php
                    # test
                ',
        ];

        yield [
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
        ];

        yield [
            '<?php //',
            '<?php #',
        ];

        // Untouched cases
        yield [
            '<?php
                    //#test
                ',
        ];

        yield [
            '<?php
                    /*
                        #test
                    */
                ',
        ];

        yield [
            '<?php // a',
            '<?php # a',
        ];

        yield [
            '<?php /* start-end */',
        ];

        yield [
            '<?php function foo(
    #[MyAttr([1, 2])] Type $myParam,
) {} // foo',
        ];
    }

    /**
     * @dataProvider provideAllCases
     */
    public function testAll(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideAllCases(): iterable
    {
        yield [
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
        ];

        yield [
            '<?php
                function foo() {
                    /* ?> */
                    return "bar";
                }',
        ];
    }
}
