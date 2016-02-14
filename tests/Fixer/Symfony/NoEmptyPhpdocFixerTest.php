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

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class NoEmptyPhpdocFixerTest extends AbstractFixerTestCase
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
                    /** a */

                    '.'

                    '.'

                    '.'

                    '.'
                    /**
                     * test
                     */

                     /** *test* */
                ',
                '<?php
                    /**  *//** a *//**  */

                    /**
                    */

                    /**
                     *
                     */

                    /** ***
                     *
                     ******/

                    /**
**/
                    /**
                     * test
                     */

                     /** *test* */
                ',
            ),
        );
    }
}
