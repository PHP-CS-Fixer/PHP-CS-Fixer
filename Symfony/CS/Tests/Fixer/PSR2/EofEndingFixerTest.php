<?php

/*
 * This file is part of the PHP CS utility.
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
final class EofEndingFixerTest extends AbstractFixerTestBase
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
                // test for not adding an empty line in empty file
                '',
            ),
            array(
                "<?php\n",
            ),
            array(
                '<?php
$a = 1;
',
                '<?php
$a = 1;',
            ),
            array(
                '<?php
$a = 2;
',
            ),
            array(
                '<?php
$a = 3;
',
                '<?php
$a = 3;


',
            ),
            array(
                "<?php\r\n\$a = 4;\n",
                "<?php\r\n\$a = 4;",
            ),
            array(
                // test not changing line break characters,
                // this is not the responsibility of this fixer
                "<?php\r\n\$a = 5;\r\n",
                "<?php\r\n\$a = 5;\r\n    \r\n",
            ),
            array(
                '<?php
$a = 6;

//test

?>
  ',
            ),
            array(
                // test for not adding an empty line after PHP tag has been closed
                '<?php
$a = 7;

//test

?>',
            ),
            array(
                // test for not adding an empty line after PHP tag has been closed
                '<?php
$a = 8;
//test
?>
Outside of PHP tags rendering


',
            ),
            array(
                // test for not adding an empty line after PHP tag has been closed
                "<?php
//test
?>
inline 1
<?php

?>Inline2\r\n",
            ),
        );
    }
}
