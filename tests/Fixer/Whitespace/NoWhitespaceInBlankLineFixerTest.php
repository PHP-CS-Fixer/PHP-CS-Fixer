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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            "<?php\n",
        ];

        yield [
            '<?php  ',
        ];

        yield [
            <<<'EOD'
                <?php

                EOD,
            '<?php'."\n  ",
        ];

        yield [
            <<<'EOD'
                <?php


                EOD,
            '<?php'."\n     ".''."\n  ",
        ];

        yield [
            <<<'EOD'
                <?php

                $a = 1;
                EOD.' ',
            '<?php'."\n     ".<<<'EOD'

                $a = 1;
                EOD.' ',
        ];

        yield [
            <<<'EOD'
                <?php
                $r = 5 +6;
                EOD.'                   '.<<<'EOD'


                $t = true> 9;
                EOD.'       '.<<<'EOD'


                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $a = 1;
                EOD.'   ',
        ];

        yield [
            <<<EOD
                <?php
                \t\$b = 1;\t\t
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    $b = 2;

                EOD,
            <<<'EOD'
                <?php
                    $b = 2;
                EOD."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $b = 3;



                EOD,
            <<<'EOD'
                <?php
                    $b = 3;
                EOD."\n    ".''."\n    ".''."\n    ",
        ];

        yield [
            <<<'EOD'
                <?php
                    $b = 4;



                    $b += 4;
                EOD,
            <<<'EOD'
                <?php
                    $b = 4;
                EOD."\n    ".''."\n    ".''."\n    ".<<<'EOD'

                    $b += 4;
                EOD,
        ];

        yield [
            "<?php\n\n\n\$b = 5;",
            "<?php\n                \n\t\n\$b = 5;",
        ];

        yield [
            "<?php\necho 1;\n?>\n\n\n\n",
        ];

        yield [
            "<?php\necho <<<HTML\ndata     \n  \n \t  \n     \nHTML\n;\n//a",
        ];

        yield [
            "<?php\n\$sql = 'SELECT * FROM products WHERE description = \"This product\n   \nis nice\"';",
        ];

        yield [
            <<<'EOD'
                <?php
                    /**
                     * @const Foo.
                     */
                    const FOO = "BAR";

                EOD,
        ];

        yield [
            "<?php\n\n    \$a = 1;\n\n    \$b = 2;",
            "<?php\n\n    \$a = 1;\n    \n    \$b = 2;",
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php\r\n\r\n    \$a = 1;\r\n\r\n    \$b = 2;",
            "<?php\r\n\r\n    \$a = 1;\r\n    \r\n    \$b = 2;",
        ];
    }
}
