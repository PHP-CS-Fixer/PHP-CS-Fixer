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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
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

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
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
                ',
            '<?php
        /*
                //
                //
                '.'
                //
                //
                //
                '.'
                //
                //
                '.'
                //
        */
                ',
        ];

        yield [
            '<?php
    // This is'.'
    //'.'
    //'.'
    // multiline comment.
    //',
            '<?php
    // This is '.'
    // '.'
    //    '.'
    // multiline comment. '.'
    // ',
        ];

        yield [
            '<?php
    /*
     * This is another'.'
     *'.'
     *'.'
     * multiline comment.'.'
     */',
            '<?php
    /* '.'
     * This is another '.'
     * '.'
     * '.'
     * multiline comment. '.'
     */',
        ];

        yield [
            '<?php
    /**
     * Summary'.'
     *'.'
     *'.'
     * Description.'.'
     *
     * @annotation
     *  Foo
     */',
            '<?php
    /** '.'
     * Summary '.'
     * '.'
     * '.'
     * Description. '.'
     * '.'
     * @annotation '.'
     *  Foo '.'
     */',
        ];

        yield [
            str_replace(
                "\n",
                "\r\n",
                '<?php
    /**
     * Summary
     *'.'
     * Description
    */',
            ),
            str_replace(
                "\n",
                "\r\n",
                '<?php
    /**
     * Summary
     * '.'
     * Description
    */',
            ),
        ];

        yield [
            str_replace(
                "\n",
                "\r",
                '<?php
    /**
     * Summary
     *'.'
     * Description
    */',
            ),
            str_replace(
                "\n",
                "\r",
                '<?php
    /**
     * Summary
     * '.'
     * Description
    */',
            ),
        ];
    }
}
