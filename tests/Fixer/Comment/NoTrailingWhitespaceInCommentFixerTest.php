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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer
 */
final class NoTrailingWhitespaceInCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                        /*
                                //
                                //

                                //
                                //
                                //

                                //
                                //

                                //
                        */
                EOD."\n                ",
            <<<'EOD'
                <?php
                        /*
                                //
                                //
                EOD."\n                ".<<<'EOD'

                                //
                                //
                                //
                EOD."\n                ".<<<'EOD'

                                //
                                //
                EOD."\n                ".<<<'EOD'

                                //
                        */
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                    // This is
                EOD.<<<'EOD'

                    //
                EOD.<<<'EOD'

                    //
                EOD.<<<'EOD'

                    // multiline comment.
                    //
                EOD,
            <<<'EOD'
                <?php
                    // This is
                EOD.' '.<<<'EOD'

                    //
                EOD.' '.<<<'EOD'

                    //
                EOD.'    '.<<<'EOD'

                    // multiline comment.
                EOD.' '.<<<'EOD'

                    //
                EOD.' ',
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     * This is another
                EOD.<<<'EOD'

                     *
                EOD.<<<'EOD'

                     *
                EOD.<<<'EOD'

                     * multiline comment.
                EOD.<<<'EOD'

                     */
                EOD,
            <<<'EOD'
                <?php
                    /*
                EOD.' '.<<<'EOD'

                     * This is another
                EOD.' '.<<<'EOD'

                     *
                EOD.' '.<<<'EOD'

                     *
                EOD.' '.<<<'EOD'

                     * multiline comment.
                EOD.' '.<<<'EOD'

                     */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /**
                     * Summary
                EOD.<<<'EOD'

                     *
                EOD.<<<'EOD'

                     *
                EOD.<<<'EOD'

                     * Description.
                EOD.<<<'EOD'

                     *
                     * @annotation
                     *  Foo
                     */
                EOD,
            <<<'EOD'
                <?php
                    /**
                EOD.' '.<<<'EOD'

                     * Summary
                EOD.' '.<<<'EOD'

                     *
                EOD.' '.<<<'EOD'

                     *
                EOD.' '.<<<'EOD'

                     * Description.
                EOD.' '.<<<'EOD'

                     *
                EOD.' '.<<<'EOD'

                     * @annotation
                EOD.' '.<<<'EOD'

                     *  Foo
                EOD.' '.<<<'EOD'

                     */
                EOD,
        ];

        yield [
            str_replace(
                "\n",
                "\r\n",
                <<<'EOD'
                    <?php
                        /**
                         * Summary
                         *
                    EOD.<<<'EOD'

                         * Description
                        */
                    EOD
            ),
            str_replace(
                "\n",
                "\r\n",
                <<<'EOD'
                    <?php
                        /**
                         * Summary
                         *
                    EOD.' '.<<<'EOD'

                         * Description
                        */
                    EOD
            ),
        ];

        yield [
            str_replace(
                "\n",
                "\r",
                <<<'EOD'
                    <?php
                        /**
                         * Summary
                         *
                    EOD.<<<'EOD'

                         * Description
                        */
                    EOD
            ),
            str_replace(
                "\n",
                "\r",
                <<<'EOD'
                    <?php
                        /**
                         * Summary
                         *
                    EOD.' '.<<<'EOD'

                         * Description
                        */
                    EOD
            ),
        ];
    }
}
