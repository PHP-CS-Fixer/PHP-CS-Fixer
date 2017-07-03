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
 * @covers \PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer
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
        return [
            [
                '<?php',
            ],
            [
                '<?php  ',
            ],
            [
                '<?php
',
                '<?php
  ',
            ],
            [
                '<?php

',
                '<?php
     '.'
  ',
            ],
            [
                '<?php

$a = 1; ',
                '<?php
     '.'
$a = 1; ',
            ],
            [
                '<?php
$r = 5 +6;                   '.'

$t = true> 9;       '.'
',
            ],
            [
                '<?php
    $a = 1;   ',
            ],
            [
                "<?php
\t\$b = 1;\t\t",
            ],
            [
                '<?php
    $b = 2;
',
                '<?php
    $b = 2;
    ',
            ],
            [
                '<?php
    $b = 3;


',
                '<?php
    $b = 3;
    '.'
    '.'
    ',
            ],
            [
                '<?php
    $b = 4;



    $b += 4;',
                '<?php
    $b = 4;
    '.'
    '.'
    '.'
    $b += 4;',
            ],
            [
                "<?php\n\n\n\$b = 5;",
                "<?php\n                \n\t\n\$b = 5;",
            ],
            [
                "<?php\necho 1;\n?>\n\n\n\n",
            ],
            [
                "<?php\necho <<<HTML\ndata     \n  \n \t  \n     \nHTML\n;\n//a",
            ],
            [
                "<?php\n\$sql = 'SELECT * FROM products WHERE description = \"This product\n   \nis nice\"';",
            ],
            [
                '<?php
    /**
     * @const Foo.
     */
    const FOO = "BAR";
',
            ],
            [
                "<?php\n\n    \$a = 1;\n\n    \$b = 2;",
                "<?php\n\n    \$a = 1;\n    \n    \$b = 2;",
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
                "<?php\r\n\r\n    \$a = 1;\r\n\r\n    \$b = 2;",
                "<?php\r\n\r\n    \$a = 1;\r\n    \r\n    \$b = 2;",
            ],
        ];
    }
}
