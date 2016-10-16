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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class SingleBlankLineAtEofFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
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
                "<?php\r\n\$a = 5;\n",
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
            array(
                "<?php return true;\n// A comment\n",
                "<?php return true;\n// A comment",
            ),
            array(
                "<?php return true;\n// A comment\n",
                "<?php return true;\n// A comment\n\n",
            ),
            array(
                "<?php return true;\n# A comment\n",
                "<?php return true;\n# A comment",
            ),
            array(
                "<?php return true;\n# A comment\n",
                "<?php return true;\n# A comment\n\n",
            ),
            array(
                "<?php return true;\n/*\nA comment\n*/\n",
                "<?php return true;\n/*\nA comment\n*/",
            ),
            array(
                "<?php return true;\n/*\nA comment\n*/\n",
                "<?php return true;\n/*\nA comment\n*/\n\n",
            ),
        );
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $fixer = clone $this->getFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input, null, $fixer);
    }

    public function provideMessyWhitespacesCases()
    {
        return array(
            array(
                "<?php\r\n\$a = 4;\r\n",
                "<?php\r\n\$a = 4;",
            ),
            array(
                "<?php\r\n\$a = 5;\r\n",
                "<?php\r\n\$a = 5;\r\n    \r\n",
            ),
        );
    }
}
