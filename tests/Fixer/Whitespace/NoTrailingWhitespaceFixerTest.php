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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield from [
            [
                '<?php
$a = 1;',
                '<?php
$a = 1;   ',
            ],
            [
                '<?php
$a = 1  ;',
                '<?php
$a = 1  ;   ',
            ],
            [
                '<?php
$b = 1;',
                '<?php
$b = 1;		',
            ],
            [
                "<?php \$b = 1;\n  ",
                "<?php \$b = 1;		\n  ",
            ],
            [
                "<?php \$b = 1;\n\$c = 1;",
                "<?php \$b = 1;   	   \n\$c = 1;",
            ],
            [
                "<?php\necho 1;\n   \necho2;",
            ],
            [
                '<?php
	$b = 1;
	',
            ],
            [
                "<?php\n\$a=1;\n      \n\t\n\$b = 1;",
            ],
            [
                "<?php\necho 1;\n?>\n\n\n\n",
            ],
            [
                "<?php\n\techo 1;\n?>\n\n\t  a \r\n	b   \r\n",
            ],
            [
                "<?php
<<<'EOT'
Il y eut un rire éclatant des écoliers qui décontenança le pauvre
garçon, si bien qu'il ne savait s'il fallait garder sa casquette à
la main, la laisser par terre ou la mettre sur sa tête. Il se
rassit et la posa sur ses genoux.
EOT;
",
            ],
            [
                "<?php\n\$string = 'x  \ny';\necho (strlen(\$string) === 5);",
            ],
            [
                "<?php\necho <<<'EOT'\nInline Il y eut un   \r\nrire éclatant    \n     \n   \r\nEOT;\n\n",
            ],
            [
                "<?php\necho 'Hello World';",
                "<?php \necho 'Hello World';",
            ],
            [
                "<?php\n\necho 'Hello World';",
                "<?php \n\necho 'Hello World';",
            ],
            [
                "<?php\r\necho 'Hello World';",
                "<?php \r\necho 'Hello World';",
            ],
            [
                "<?php\necho 'Hello World';",
                "<?php  \necho 'Hello World';",
            ],
            [
                "<?php\necho 'Hello World';",
                "<?php	\necho 'Hello World';",
            ],
            [
                '<?php ',
                '<?php  ',
            ],
            [
                "<?php\t",
                "<?php\t\t",
            ],
            [
                '<?php ', // do not trim this as "<?php" is not valid PHP
            ],
            [
                "<?php\n      \n   \n    ",
            ],
            [
                "<?php\n   \n    ",
                "<?php      \n   \n    ",
            ],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php class Foo {
    #[Required]
    public $bar;
}',
            '<?php class Foo {
    #[Required]     '.'
    public $bar;
}',
        ];
    }
}
