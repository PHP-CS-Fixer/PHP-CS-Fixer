<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class WhitespacyLinesFixerTest extends AbstractFixerTestBase
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
    $a = 1;   ',
            ),
            array(
                '<?php
	$b = 1;		',
            ),
            array(
                "<?php\n\n\n\$b = 1;",
                "<?php\n                \n	\n\$b = 1;",
            ),
            array(
                "<?php\necho 1;\n?>\n\n\n\n",
            ),
            array(
                "<?php\necho <<<HTML\ndata     \n  \n \t  \n     \nHTML\n;\n//a",

            ),
        );
    }
}
