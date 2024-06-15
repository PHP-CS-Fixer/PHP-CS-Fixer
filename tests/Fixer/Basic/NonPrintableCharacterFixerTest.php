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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Ivan Boprzenkov <ivan.borzenkov@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer
 */
final class NonPrintableCharacterFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, null|string, array{use_escape_sequences_in_strings: bool}}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php echo "Hello World !";',
            '<?php echo "'.pack('H*', 'e2808b').'Hello'.pack('H*', 'e28087').'World'.pack('H*', 'c2a0').'!";',
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php echo "Hello World !";',
            '<?php echo "'.
                pack('H*', 'e2808b').
                pack('H*', 'e2808b').
                pack('H*', 'e2808b').
                pack('H*', 'e2808b').
                pack('H*', 'e2808b').
                pack('H*', 'e2808b').
            'Hello World !";',
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php
// echo
echo "Hello World !";',
            '<?php
// ec'.pack('H*', 'e2808b').'ho
echo "Hello'.pack('H*', 'e280af').'World'.pack('H*', 'c2a0').'!";',
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php

                /**
                 * @param string $p Param
                 */
                function f(string $p)
                {
                    echo $p;
                }',
            '<?php

                /**
                 * @param '.pack('H*', 'e2808b').'string $p Param
                 */
                function f(string $p)
                {
                    echo $p;
                }',
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php echo "$a[0] ${a}";',
            '<?php echo "$a'.pack('H*', 'e2808b').'[0]'.pack('H*', 'e2808b').' ${a'.pack('H*', 'e2808b').'}";',
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php echo \'12345\';?>abc<?php ?>',
            '<?php echo \'123'.pack('H*', 'e2808b').'45\';?>a'.pack('H*', 'e2808b').'bc<?php ?>',
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php echo "${foo'.pack('H*', 'c2a0').'bar} is great!";',
            null,
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php echo $foo'.pack('H*', 'c2a0').'bar;',
            null,
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php /* foo *'.pack('H*', 'e2808b').'/ bar */',
            null,
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php /** foo *'.pack('H*', 'e2808b').'/ bar */',
            null,
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php echo b"Hello World !";',
            '<?php echo b"'.pack('H*', 'e2808b').'Hello'.pack('H*', 'e28087').'World'.pack('H*', 'c2a0').'!";',
            ['use_escape_sequences_in_strings' => false],
        ];

        yield [
            '<?php

                /**
                 * @param string $p Param
                 */
                function f(string $p)
                {
                    echo $p;
                }',
            '<?php

                /**
                 * @param '.pack('H*', 'e2808b').'string $p Param
                 */
                function f(string $p)
                {
                    echo $p;
                }',
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo \'FooBar\\\\\';',
            null,
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo "Foo\u{200b}Bar";',
            '<?php echo "Foo'.pack('H*', 'e2808b').'Bar";',
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo "Foo\u{200b}Bar";',
            '<?php echo \'Foo'.pack('H*', 'e2808b').'Bar\';',
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo "Foo\u{200b} Bar \\\n \\\ \$variableToEscape";',
            '<?php echo \'Foo'.pack('H*', 'e2808b').' Bar \n \ $variableToEscape\';',
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo <<<\'TXT\'
FooBar\
TXT;
',
            null,
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo <<<TXT
Foo\u{200b}Bar
TXT;
',
            '<?php echo <<<TXT
Foo'.pack('H*', 'e2808b').'Bar
TXT;
',
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo <<<TXT
Foo\u{200b}Bar
TXT;
',
            '<?php echo <<<\'TXT\'
Foo'.pack('H*', 'e2808b').'Bar
TXT;
',
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo <<<TXT
Foo\u{200b} Bar \\\n \\\ \$variableToEscape
TXT;
',
            '<?php echo <<<\'TXT\'
Foo'.pack('H*', 'e2808b').' Bar \n \ $variableToEscape
TXT;
',
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            '<?php echo \'。\';',
            null,
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "Double \" quote \u{200b} inside";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo 'Double " quote %s inside';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "Single ' quote \u{200b} inside";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo 'Single \' quote %s inside';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo <<<STRING
                    Quotes ' and " to be handled \u{200b} properly \\' and \\"
                STRING
                ;
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo <<<'STRING'
                        Quotes ' and " to be handled %s properly \' and \"
                    STRING
                    ;
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "\\\u{200b}\"";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo '\\%s"';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "\\\u{200b}'";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo '\\%s\'';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "Backslash 1 \\ \u{200b}";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo 'Backslash 1 \ %s';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "Backslash 2 \\ \u{200b}";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo 'Backslash 2 \\ %s';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "Backslash 3 \\\\ \u{200b}";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo 'Backslash 3 \\\ %s';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            <<<'EXPECTED'
                <?php echo "Backslash 4 \\\\ \u{200b}";
                EXPECTED,
            sprintf(
                <<<'INPUT'
                    <?php echo 'Backslash 4 \\\\ %s';
                    INPUT
                ,
                pack('H*', 'e2808b')
            ),
            ['use_escape_sequences_in_strings' => true],
        ];

        yield [
            "<?php \"String in single quotes, having non-breaking space: \\u{a0}, linebreak: \n, and single quote inside: ' is a dangerous mix.\";",
            "<?php 'String in single quotes, having non-breaking space: ".pack('H*', 'c2a0').", linebreak: \n, and single quote inside: \\' is a dangerous mix.';",
            ['use_escape_sequences_in_strings' => true],
        ];
    }
}
