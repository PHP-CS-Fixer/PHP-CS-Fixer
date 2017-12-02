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

use PhpCsFixer\Fixer\FixerInterface;
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
        return [
            [
                'dollar_close_curly_braces',
                'DollarCloseCurlyBraces',
            ],
            [
                'utf8_encoder_fixer',
                'utf8EncoderFixer',
            ],
            [
                'terminated_with_number10',
                'TerminatedWithNumber10',
            ],
            [
                'utf8_encoder_fixer',
            ],
        ];
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
        return [
            [0,    1,   1],
            [0,   -1,  -1],
            [-1,  10,  20],
            [-1, -20, -10],
            [1,   20,  10],
            [1,  -10, -20],
        ];
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
        return [
            [
                ["\t aaa\n", " bbb\n", "\t"],
                "\t aaa\n bbb\n\t",
            ],
            [
                ["aaa\r\n", " bbb\r\n"],
                "aaa\r\n bbb\r\n",
            ],
            [
                ["aaa\r\n", " bbb\n"],
                "aaa\r\n bbb\n",
            ],
            [
                ["aaa\r\n\n\n\r\n", " bbb\n"],
                "aaa\r\n\n\n\r\n bbb\n",
            ],
        ];
    }

    /**
     * @param string       $spaces
     * @param array|string $input  token prototype
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
        return [
            ['    ', [T_WHITESPACE, "\n\n    "]],
            [' ', [T_WHITESPACE, "\r\n\r\r\r "]],
            ["\t", [T_WHITESPACE, "\r\n\t"]],
            ['', [T_WHITESPACE, "\t\n\r"]],
            ['', [T_WHITESPACE, "\n"]],
            ['', ''],
        ];
    }

    public function testCalculateTrailingWhitespaceIndentFail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given token must be whitespace, got "T_STRING".');

        $token = new Token([T_STRING, 'foo']);

        Utils::calculateTrailingWhitespaceIndent($token);
    }

    /**
     * @dataProvider provideStableSortCases
     */
    public function testStableSort(
        array $expected,
        array $elements,
        callable $getComparableValueCallback,
        callable $compareValuesCallback
    ) {
        $this->assertSame(
            $expected,
            Utils::stableSort($elements, $getComparableValueCallback, $compareValuesCallback)
        );
    }

    public function provideStableSortCases()
    {
        return [
            [
                ['a', 'b', 'c', 'd', 'e'],
                ['b', 'd', 'e', 'a', 'c'],
                function ($element) { return $element; },
                'strcmp',
            ],
            [
                ['b', 'd', 'e', 'a', 'c'],
                ['b', 'd', 'e', 'a', 'c'],
                function ($element) { return 'foo'; },
                'strcmp',
            ],
            [
                ['b', 'd', 'e', 'a', 'c'],
                ['b', 'd', 'e', 'a', 'c'],
                function ($element) { return $element; },
                function ($a, $b) { return 0; },
            ],
            [
                ['bar1', 'baz1', 'foo1', 'bar2', 'baz2', 'foo2'],
                ['foo1', 'foo2', 'bar1', 'bar2', 'baz1', 'baz2'],
                function ($element) { return preg_replace('/([a-z]+)(\d+)/', '$2$1', $element); },
                'strcmp',
            ],
        ];
    }

    public function testSortFixers()
    {
        $fixers = [
            $this->createFixerDouble('f1', 0),
            $this->createFixerDouble('f2', -10),
            $this->createFixerDouble('f3', 10),
            $this->createFixerDouble('f4', -10),
        ];

        $this->assertSame(
            [
                $fixers[2],
                $fixers[0],
                $fixers[1],
                $fixers[3],
            ],
            Utils::sortFixers($fixers)
        );
    }

    public function testNaturalLanguageJoinWithBackticksThrowsInvalidArgumentExceptionForEmptyArray()
    {
        $this->expectException(\InvalidArgumentException::class);

        Utils::naturalLanguageJoinWithBackticks([]);
    }

    /**
     * @dataProvider provideNaturalLanguageJoinWithBackticksCases
     *
     * @param string $joined
     * @param array  $names
     */
    public function testNaturalLanguageJoinWithBackticks($joined, array $names)
    {
        $this->assertSame($joined, Utils::naturalLanguageJoinWithBackticks($names));
    }

    public function provideNaturalLanguageJoinWithBackticksCases()
    {
        return [
            [
                '`a`',
                ['a'],
            ],
            [
                '`a` and `b`',
                ['a', 'b'],
            ],
            [
                '`a`, `b` and `c`',
                ['a', 'b', 'c'],
            ],
        ];
    }

    private function createFixerDouble($name, $priority)
    {
        $fixer = $this->prophesize(FixerInterface::class);
        $fixer->getName()->willReturn($name);
        $fixer->getPriority()->willReturn($priority);

        return $fixer->reveal();
    }
}
