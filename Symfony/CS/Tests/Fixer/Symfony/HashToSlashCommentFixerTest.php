<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class HashToSlashCommentFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
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
        );
    }
}
