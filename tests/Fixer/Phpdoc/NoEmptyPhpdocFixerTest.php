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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer
 */
final class NoEmptyPhpdocFixerTest extends AbstractFixerTestCase
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
        return [
            [
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
            ],
        ];
    }
}
