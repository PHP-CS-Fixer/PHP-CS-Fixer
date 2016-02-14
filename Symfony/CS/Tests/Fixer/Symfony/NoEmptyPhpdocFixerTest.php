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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class NoEmptyPhpdocFixerTest extends AbstractFixerTestBase
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
