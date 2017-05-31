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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Test\AbstractFixerTestCase;

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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFixWithoutEscapeSequences($expected, $input = null)
    {
        $this->fixer->configure([
            'use_escape_sequences_in_strings' => false,
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php echo "Hello World !";',
                '<?php echo "'.pack('H*', 'e2808b').'Hello'.pack('H*', 'e28087').'World'.pack('H*', 'c2a0').'!";',
            ],
            [
                '<?php echo "Hello World !";',
                '<?php echo "'.
                    pack('H*', 'e2808b').
                    pack('H*', 'e2808b').
                    pack('H*', 'e2808b').
                    pack('H*', 'e2808b').
                    pack('H*', 'e2808b').
                    pack('H*', 'e2808b').
                'Hello World !";',
            ],
            [
                '<?php
// echo
echo "Hello World !";',
                '<?php
// ec'.pack('H*', 'e2808b').'ho
echo "Hello'.pack('H*', 'e280af').'World'.pack('H*', 'c2a0').'!";',
            ],
            [
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
            ],
            [
                '<?php echo "$a[0] ${a}";',
                '<?php echo "$a'.pack('H*', 'e2808b').'[0]'.pack('H*', 'e2808b').' ${a'.pack('H*', 'e2808b').'}";',
            ],
            [
                '<?php echo \'12345\';?>abc<?php ?>',
                '<?php echo \'123'.pack('H*', 'e2808b').'45\';?>a'.pack('H*', 'e2808b').'bc<?php ?>',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithEscapeSequencesInStringsCases
     * @requires PHP 7.0
     */
    public function testFixWithEscapeSequencesInStrings($expected, $input = null)
    {
        $this->fixer->configure([
            'use_escape_sequences_in_strings' => true,
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFixWithEscapeSequencesInStringsCases()
    {
        return [
            [
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
            ],
            [
                '<?php echo \'FooBar\\\\\';',
            ],
            [
                '<?php echo "Foo\u{200b}Bar";',
                '<?php echo "Foo'.pack('H*', 'e2808b').'Bar";',
            ],
            [
                '<?php echo "Foo\u{200b}Bar";',
                '<?php echo \'Foo'.pack('H*', 'e2808b').'Bar\';',
            ],
            [
                '<?php echo "Foo\u{200b} Bar \\\\n \\\\ \$variableToEscape";',
                '<?php echo \'Foo'.pack('H*', 'e2808b').' Bar \n \ $variableToEscape\';',
            ],
            [
                '<?php echo <<<\'TXT\'
FooBar\
TXT;
',
            ],
            [
                '<?php echo <<<TXT
Foo\u{200b}Bar
TXT;
',
                '<?php echo <<<TXT
Foo'.pack('H*', 'e2808b').'Bar
TXT;
',
            ],
            [
                '<?php echo <<<TXT
Foo\u{200b}Bar
TXT;
',
                '<?php echo <<<\'TXT\'
Foo'.pack('H*', 'e2808b').'Bar
TXT;
',
            ],
            [
                '<?php echo <<<TXT
Foo\u{200b} Bar \\\\n \\\\ \$variableToEscape
TXT;
',
                '<?php echo <<<\'TXT\'
Foo'.pack('H*', 'e2808b').' Bar \n \ $variableToEscape
TXT;
',
            ],
            [
                '<?php echo \'。\';',
            ],
        ];
    }

    /**
     * @requires PHP <7.0
     */
    public function testFixWithEscapeSequencesInStringsLowerThanPhp70()
    {
        $this->setExpectedExceptionRegExp(
            InvalidFixerConfigurationException::class,
            '/^\[non_printable_character\] Invalid configuration: Escape sequences require PHP 7\.0\+\.$/'
        );

        $this->fixer->configure([
            'use_escape_sequences_in_strings' => true,
        ]);
    }
}
