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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer
 */
final class SingleBlankLineAtEofFixerTest extends AbstractFixerTestCase
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
            'Not adding an empty line in empty file.' => [
                '',
            ],
            'Not adding an empty line in file with only white space.' => [
                '  ',
            ],
            [
                "<?php\n",
            ],
            [
                '<?php
$a = 1;
',
                '<?php
$a = 1;',
            ],
            [
                '<?php
$a = 2;
',
            ],
            [
                '<?php
$a = 3;
',
                '<?php
$a = 3;


',
            ],
            [
                "<?php\r\n\$a = 4;\n",
                "<?php\r\n\$a = 4;",
            ],
            [
                "<?php\r\n\$a = 5;\n",
                "<?php\r\n\$a = 5;\r\n    \r\n",
            ],
            [
                '<?php
$a = 6;

//test

?>
  ',
            ],
            [
                // test for not adding an empty line after PHP tag has been closed
                '<?php
$a = 7;

//test

?>',
            ],
            [
                // test for not adding an empty line after PHP tag has been closed
                '<?php
$a = 8;
//test
?>
Outside of PHP tags rendering


',
            ],
            [
                // test for not adding an empty line after PHP tag has been closed
                "<?php
//test
?>
inline 1
<?php

?>Inline2\r\n",
            ],
            [
                "<?php return true;\n// A comment\n",
                "<?php return true;\n// A comment",
            ],
            [
                "<?php return true;\n// A comment\n",
                "<?php return true;\n// A comment\n\n",
            ],
            [
                "<?php return true;\n# A comment\n",
                "<?php return true;\n# A comment",
            ],
            [
                "<?php return true;\n# A comment\n",
                "<?php return true;\n# A comment\n\n",
            ],
            [
                "<?php return true;\n/*\nA comment\n*/\n",
                "<?php return true;\n/*\nA comment\n*/",
            ],
            [
                "<?php return true;\n/*\nA comment\n*/\n",
                "<?php return true;\n/*\nA comment\n*/\n\n",
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php\r\n\$a = 4;\r\n",
                "<?php\r\n\$a = 4;",
            ],
            [
                "<?php\r\n\$a = 5;\r\n",
                "<?php\r\n\$a = 5;\r\n    \r\n",
            ],
        ];
    }
}
