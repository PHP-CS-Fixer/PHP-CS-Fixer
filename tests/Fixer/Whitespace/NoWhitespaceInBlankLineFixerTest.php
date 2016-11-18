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
final class NoWhitespaceInBlankLineFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
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
                '<?php
$r = 5 +6;                   '.'

$t = true> 9;       '.'
',
            ),
            array(
                '<?php
    $a = 1;   ',
            ),
            array(
                "<?php
\t\$b = 1;\t\t",
            ),
            array(
                '<?php
    $b = 2;
',
                '<?php
    $b = 2;
    ',
            ),
            array(
                '<?php
    $b = 3;


',
                '<?php
    $b = 3;
    '.'
    '.'
    ',
            ),
            array(
                '<?php
    $b = 4;



    $b += 4;',
                '<?php
    $b = 4;
    '.'
    '.'
    '.'
    $b += 4;',
            ),
            array(
                "<?php\n\n\n\$b = 5;",
                "<?php\n                \n\t\n\$b = 5;",
            ),
            array(
                "<?php\necho 1;\n?>\n\n\n\n",
            ),
            array(
                "<?php\necho <<<HTML\ndata     \n  \n \t  \n     \nHTML\n;\n//a",
            ),
            array(
                "<?php\n\$sql = 'SELECT * FROM products WHERE description = \"This product\n   \nis nice\"';",
            ),
            array(
                '<?php
    /**
     * @const Foo.
     */
    const FOO = "BAR";
',
            ),
            array(
                "<?php\n\n    \$a = 1;\n\n    \$b = 2;",
                "<?php\n\n    \$a = 1;\n    \n    \$b = 2;",
            ),
        );
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
        return array(
            array(
                "<?php\r\n\r\n    \$a = 1;\r\n\r\n    \$b = 2;",
                "<?php\r\n\r\n    \$a = 1;\r\n    \r\n    \$b = 2;",
            ),
        );
    }
}
