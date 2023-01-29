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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer
 */
final class NoEmptyPhpdocFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
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
