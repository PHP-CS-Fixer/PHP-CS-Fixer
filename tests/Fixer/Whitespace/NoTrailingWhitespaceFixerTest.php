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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer
 */
final class NoTrailingWhitespaceFixerTest extends AbstractFixerTestCase
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
                "<?php \$b = 1;\n  ",
                "<?php \$b = 1;		\n  ",
            ),
            array(
                "<?php \$b = 1;\n\$c = 1;",
                "<?php \$b = 1;   	   \n\$c = 1;",
            ),
            array(
                "<?php\necho 1;\n   \necho2;",
            ),
            array(
                '<?php
	$b = 1;
	',
            ),
            array(
                "<?php\n\$a=1;\n      \n\t\n\$b = 1;",
            ),
            array(
                "<?php\necho 1;\n?>\n\n\n\n",
            ),
            array(
                "<?php\n\techo 1;\n?>\n\n\t  a \r\n	b   \r\n",
            ),
            array(
                "<?php
<<<'EOT'
Il y eut un rire éclatant des écoliers qui décontenança le pauvre
garçon, si bien qu'il ne savait s'il fallait garder sa casquette à
la main, la laisser par terre ou la mettre sur sa tête. Il se
rassit et la posa sur ses genoux.
EOT;
",
            ),
            array(
                "<?php\n\$string = 'x  \ny';\necho (strlen(\$string) === 5);",
            ),
            array(
                "<?php\necho <<<'EOT'\nInline Il y eut un   \r\nrire éclatant    \n     \n   \r\nEOT;\n\n",
            ),
        );
    }
}
