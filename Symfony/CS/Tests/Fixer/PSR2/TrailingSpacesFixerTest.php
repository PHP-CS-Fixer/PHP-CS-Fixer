<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TrailingSpacesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
$a = 1;',
                '<?php
$a = 1;   ',
            ),
            array(
                '<?php
$a = 1  ;',
                '<?php
$a = 1  ;   ',
            ),
            array(
                '<?php
$b = 1;',
                '<?php
$b = 1;		',
            ),
            array(
                '<?php
$b = 1;',
                '<?php
$b = 1;   	   ',
            ),
            array(
                "<?php\n      \n	\n\$b = 1;",
            ),
        );
    }
}
