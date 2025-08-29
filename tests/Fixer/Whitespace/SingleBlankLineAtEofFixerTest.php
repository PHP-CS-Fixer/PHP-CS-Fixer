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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class SingleBlankLineAtEofFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'Not adding an empty line in empty file.' => [
            '',
        ];

        yield 'Not adding an empty line in file with only white space.' => [
            '  ',
        ];

        yield [
            "<?php\n",
        ];

        yield [
            '<?php
$a = 1;
',
            '<?php
$a = 1;',
        ];

        yield [
            '<?php
$a = 2;
',
        ];

        yield [
            '<?php
$a = 3;
',
            '<?php
$a = 3;


',
        ];

        yield [
            "<?php\r\n\$a = 4;\n",
            "<?php\r\n\$a = 4;",
        ];

        yield [
            "<?php\r\n\$a = 5;\n",
            "<?php\r\n\$a = 5;\r\n    \r\n",
        ];

        yield [
            '<?php
$a = 6;

//test

?>
  ',
        ];

        yield [
            // test for not adding an empty line after PHP tag has been closed
            '<?php
$a = 7;

//test

?>',
        ];

        yield [
            // test for not adding an empty line after PHP tag has been closed
            '<?php
$a = 8;
//test
?>
Outside of PHP tags rendering


',
        ];

        yield [
            // test for not adding an empty line after PHP tag has been closed
            "<?php
//test
?>
inline 1
<?php

?>Inline2\r\n",
        ];

        yield [
            "<?php return true;\n// A comment\n",
            "<?php return true;\n// A comment",
        ];

        yield [
            "<?php return true;\n// A comment\n",
            "<?php return true;\n// A comment\n\n",
        ];

        yield [
            "<?php return true;\n# A comment\n",
            "<?php return true;\n# A comment",
        ];

        yield [
            "<?php return true;\n# A comment\n",
            "<?php return true;\n# A comment\n\n",
        ];

        yield [
            "<?php return true;\n/*\nA comment\n*/\n",
            "<?php return true;\n/*\nA comment\n*/",
        ];

        yield [
            "<?php return true;\n/*\nA comment\n*/\n",
            "<?php return true;\n/*\nA comment\n*/\n\n",
        ];

        yield [
            "<?= 1;\n",
            '<?= 1;',
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

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php\r\n\$a = 4;\r\n",
            "<?php\r\n\$a = 4;",
        ];

        yield [
            "<?php\r\n\$a = 5;\r\n",
            "<?php\r\n\$a = 5;\r\n    \r\n",
        ];
    }
}
