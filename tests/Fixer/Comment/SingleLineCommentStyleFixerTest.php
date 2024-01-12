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
            <<<'EOD'
                <?php
                // lonely line

                EOD,
            <<<'EOD'
                <?php
                /* lonely line */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                   // indented line

                EOD,
            <<<'EOD'
                <?php
                   /* indented line */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                   // weird-spaced line

                EOD,
            <<<'EOD'
                <?php
                   /*   weird-spaced line*/

                EOD,
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
            <<<'EOD'
                <?php
                $a = 1; // after code

                EOD,
            <<<'EOD'
                <?php
                $a = 1; /* after code */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                   /* first */ // second

                EOD,
            <<<'EOD'
                <?php
                   /* first */ /* second */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                   /* first */// second
                EOD,
            <<<'EOD'
                <?php
                   /* first *//*
                   second
                   */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    // one line
                EOD,
            <<<'EOD'
                <?php
                    /*one line

                     */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    // one line
                EOD,
            <<<'EOD'
                <?php
                    /*

                    one line*/
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    // one line
                EOD,
            <<<EOD
                <?php
                    /* \t
                EOD.' '.<<<EOD

                 \t   * one line
                EOD.' '.<<<'EOD'

                     *
                     */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                //
                EOD,
            <<<'EOD'
                <?php
                /***
                 *
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                    // s
                EOD,
            <<<'EOD'
                <?php

                    /***
                s    *
                     */
                EOD,
        ];

        yield 'empty comment' => [
            <<<'EOD'
                <?php
                //

                EOD,
            <<<'EOD'
                <?php
                /**/

                EOD,
        ];

        // Untouched cases
        yield [
            <<<'EOD'
                <?php
                $a = 1; /* in code */ $b = 2;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     * in code 2
                     */ $a = 1;

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /***
                 *
                 */ $a = 1;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /***
                s    *
                     */ $a = 1;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     * first line
                     * second line
                     */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     * first line
                     *
                     * second line
                     */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*first line
                second line*/
                EOD,
        ];

        yield [
            '<?php /** inline doc comment */',
        ];

        yield [
            <<<'EOD'
                <?php
                    /**
                     * Doc comment
                     */
                EOD,
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
            <<<'EOD'
                <?php
                                    // test
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    # test
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    // test1
                                    //test2
                                    // test3
                                    // test 4
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    # test1
                                    #test2
                                    # test3
                                    # test 4
                EOD."\n                ",
        ];

        yield [
            '<?php //',
            '<?php #',
        ];

        // Untouched cases
        yield [
            <<<'EOD'
                <?php
                                    //#test
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    /*
                                        #test
                                    */
                EOD."\n                ",
        ];

        yield [
            '<?php // a',
            '<?php # a',
        ];

        yield [
            '<?php /* start-end */',
        ];

        yield [
            <<<'EOD'
                <?php function foo(
                    #[MyAttr([1, 2])] Type $myParam,
                ) {} // foo
                EOD,
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
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
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

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                function foo() {
                                    /* ?> */
                                    return "bar";
                                }
                EOD,
        ];
    }
}
