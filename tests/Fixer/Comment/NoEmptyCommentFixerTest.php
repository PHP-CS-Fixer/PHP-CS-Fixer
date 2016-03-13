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
final class NoEmptyCommentFixerTest extends AbstractFixerTestCase
{
    /**
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
                '<?php
                    '.'
                ',
                '<?php
                    //
                ',
            ),
            array(
                '<?php
                    '.'
                ',
                '<?php
                    #
                ',
            ),
            array(
                '<?php
                    '.'
                ',
                '<?php
                    /**/
                ',
            ),
            array(
                '<?php
                    echo 1;'.'
                ',
                '<?php
                    echo 1;//
                ',
            ),
            array(
                '<?php
                echo 2;
                    '.'
echo 1;
                ',
                '<?php
                echo 2;
                    //
echo 1;
                ',
            ),
            array(
                '<?php
                    echo 0;'.'
echo 1;
                ',
                '<?php
                    echo 0;//
echo 1;
                ',
            ),
            array(
                '<?php
                    echo 0;echo 1;
                ',
                '<?php
                    echo 0;/**/echo 1;
                ',
            ),
            array(
                '<?php
                ',
                '<?php
                //',
            ),
            array(
                '<?php
                ',
                '<?php
                /*


                */',
            ),
            array(
                '<?php

                ?>',
                '<?php

                //?>',
            ),
            array(
                '<?php
                    '.'
                    '.'
                    '.'
                    '.'
                ',
                '<?php
                    //
                    //
                    //
                    /**///
                ',
            ),
            array(
                '<?php
                // a
            // /**/
              // #
/* b */ // s
          #                        c',
            ),
            array(
                '<?php
                '.'
                ',
            ),
        );
    }
}
