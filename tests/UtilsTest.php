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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 * @author Odín del Río <odin.drp@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Utils
 */
final class UtilsTest extends TestCase
{
    /**
     * @param string $expected Camel case string
     * @param string $input    Input string
     *
     * @dataProvider provideCamelCaseToUnderscoreCases
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
     * @param int $expected
     * @param int $left
     * @param int $right
     *
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
     * @param array  $expected
     * @param string $input
     *
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
     * @param string       $spaces
     * @param string|array $input  token prototype
     *
     * @dataProvider provideCalculateTrailingWhitespaceIndentCases
     */
    public function testCalculateTrailingWhitespaceIndent($spaces, $input)
    {
        $token = new Token($input);

        $this->assertSame($spaces, Utils::calculateTrailingWhitespaceIndent($token));
    }

    public function provideCalculateTrailingWhitespaceIndentCases()
    {
        return array(
            array('    ', array(T_WHITESPACE, "\n\n    ")),
            array(' ', array(T_WHITESPACE, "\r\n\r\r\r ")),
            array("\t", array(T_WHITESPACE, "\r\n\t")),
            array('', array(T_WHITESPACE, "\t\n\r")),
            array('', array(T_WHITESPACE, "\n")),
            array('', ''),
        );
    }

    public function testCalculateTrailingWhitespaceIndentFail()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'The given token must be whitespace, got "T_STRING".'
        );

        $token = new Token(array(T_STRING, 'foo'));

        Utils::calculateTrailingWhitespaceIndent($token);
    }
}
