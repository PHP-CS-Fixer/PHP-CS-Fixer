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
 * @author SpacePossum
 *
 * @internal
 */
final class HashToSlashCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<h1>This is an <?php //echo 123;?> example</h1>',
                '<h1>This is an <?php #echo 123;?> example</h1>',
            ),
            array(
                '<?php
                    //#test
                ',
            ),
            array(
                '<?php
                    /*
                        #test
                    */
                ',
            ),
            array(
                '<?php
                    // test
                ',
                '<?php
                    # test
                ',
            ),
            array(
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
            ),
            array(
                '<?php // a',
                '<?php # a',
            ),
        );
    }
}
