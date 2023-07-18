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
        yield [
            '<?php
$a = 1;',
            '<?php
$a = 1;   ',
        ];

        yield [
            '<?php
$a = 1  ;',
            '<?php
$a = 1  ;   ',
        ];

        yield [
            '<?php
$b = 1;',
            '<?php
$b = 1;		',
        ];

        yield [
            "<?php \$b = 1;\n  ",
            "<?php \$b = 1;		\n  ",
        ];

        yield [
            "<?php \$b = 1;\n\$c = 1;",
            "<?php \$b = 1;   	   \n\$c = 1;",
        ];

        yield [
            "<?php\necho 1;\n   \necho2;",
        ];

        yield [
            '<?php
	$b = 1;
	',
        ];

        yield [
            "<?php\n\$a=1;\n      \n\t\n\$b = 1;",
        ];

        yield [
            "<?php\necho 1;\n?>\n\n\n\n",
        ];

        yield [
            "<?php\n\techo 1;\n?>\n\n\t  a \r\n	b   \r\n",
        ];

        yield [
            "<?php
<<<'EOT'
Il y eut un rire éclatant des écoliers qui décontenança le pauvre
garçon, si bien qu'il ne savait s'il fallait garder sa casquette à
la main, la laisser par terre ou la mettre sur sa tête. Il se
rassit et la posa sur ses genoux.
EOT;
",
        ];

        yield [
            "<?php\n\$string = 'x  \ny';\necho (strlen(\$string) === 5);",
        ];

        yield [
            "<?php\necho <<<'EOT'\nInline Il y eut un   \r\nrire éclatant    \n     \n   \r\nEOT;\n\n",
        ];

        yield [
            "<?php\necho 'Hello World';",
            "<?php \necho 'Hello World';",
        ];

        yield [
            "<?php\n\necho 'Hello World';",
            "<?php \n\necho 'Hello World';",
        ];

        yield [
            "<?php\r\necho 'Hello World';",
            "<?php \r\necho 'Hello World';",
        ];

        yield [
            "<?php\necho 'Hello World';",
            "<?php  \necho 'Hello World';",
        ];

        yield [
            "<?php\necho 'Hello World';",
            "<?php	\necho 'Hello World';",
        ];

        yield [
            '<?php ',
            '<?php  ',
        ];

        yield [
            "<?php\t",
            "<?php\t\t",
        ];

        yield [
            '<?php ', // do not trim this as "<?php" is not valid PHP
        ];

        yield [
            "<?php\n      \n   \n    ",
        ];

        yield [
            "<?php\n   \n    ",
            "<?php      \n   \n    ",
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
