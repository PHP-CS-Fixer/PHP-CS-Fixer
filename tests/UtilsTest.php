<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Utils;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 * @author Odín del Río <odin.drp@gmail.com>
 *
 * @internal
 */
final class UtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCamelCaseToUnderscoreCases
     *
     * @param string $expected Camel case string.
     * @param string $input    Input string.
     */
    public function testCamelCaseToUnderscore($expected, $input = null)
    {
        if (null !== $input) {
            $this->assertSame($expected, Utils::camelCaseToUnderscore($input));
        }

        $this->assertSame($expected, Utils::camelCaseToUnderscore($expected));
    }

    /**
     * @return array
     */
    public function provideCamelCaseToUnderscoreCases()
    {
        return array(
            array(
                'dollar_close_curly_braces',
                'DollarCloseCurlyBraces',
            ),
            array(
                'utf8_encoder_fixer',
                'utf8EncoderFixer',
            ),
            array(
                'terminated_with_number10',
                'TerminatedWithNumber10',
            ),
            array(
                'utf8_encoder_fixer',
            ),

        );
    }

    /**
     * @dataProvider provideCmpIntCases
     */
    public function testCmpInt($expected, $left, $right)
    {
        $this->assertSame($expected, Utils::cmpInt($left, $right));
    }

    public function provideCmpIntCases()
    {
        return array(
            array(0,    1,   1),
            array(0,   -1,  -1),
            array(-1,  10,  20),
            array(-1, -20, -10),
            array(1,   20,  10),
            array(1,  -10, -20),
        );
    }

    /**
     * @dataProvider provideSplitLinesCases
     */
    public function testSplitLines(array $expected, $input)
    {
        $this->assertSame($expected, Utils::splitLines($input));
    }

    public function provideSplitLinesCases()
    {
        return array(
            array(
                array("\t aaa\n", " bbb\n", "\t"),
                "\t aaa\n bbb\n\t",
            ),
            array(
                array("aaa\r\n", " bbb\r\n"),
                "aaa\r\n bbb\r\n",
            ),
            array(
                array("aaa\r\n", " bbb\n"),
                "aaa\r\n bbb\n",
            ),
            array(
                array("aaa\r\n\n\n\r\n", " bbb\n"),
                "aaa\r\n\n\n\r\n bbb\n",
            ),
        );
    }

    /**
     * @dataProvider provideCalculateTrailingWhitespaceIndentCases
     */
    public function testCalculateTrailingWhitespaceIndent($spaces, $input)
    {
        $token = new Token(array(T_WHITESPACE, $input));

        $this->assertSame($spaces, Utils::calculateTrailingWhitespaceIndent($token));
    }

    public function provideCalculateTrailingWhitespaceIndentCases()
    {
        return array(
            array('    ', "\n\n    "),
            array(' ', "\r\n\r\r\r "),
            array("\t", "\r\n\t"),
            array('', "\t\n\r"),
            array('', "\n"),
            array('', ''),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The given token must be whitespace, got "T_STRING".
     */
    public function testCalculateTrailingWhitespaceIndentFail()
    {
        $token = new Token(array(T_STRING, 'foo'));

        Utils::calculateTrailingWhitespaceIndent($token);
    }
}
