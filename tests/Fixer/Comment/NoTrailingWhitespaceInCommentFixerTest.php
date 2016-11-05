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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class NoTrailingWhitespaceInCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
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
            ),
            array(
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
            ),
            array(
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
            ),
        );
    }
}
