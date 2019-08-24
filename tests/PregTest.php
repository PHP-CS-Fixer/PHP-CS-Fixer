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

use PhpCsFixer\Preg;
use PhpCsFixer\PregException;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers \PhpCsFixer\Preg
 *
 * @internal
 */
final class PregTest extends TestCase
{
    public function testMatchFailing()
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::match(): Invalid PCRE pattern ""');

        Preg::match('', 'foo', $matches);
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     */
    public function testMatch($pattern, $subject)
    {
        $expectedResult = preg_match($pattern, $subject, $expectedMatches);
        $actualResult = Preg::match($pattern, $subject, $actualMatches);

        static::assertSame($expectedResult, $actualResult);
        static::assertSame($expectedMatches, $actualMatches);
    }

    public function providePatternValidationCases()
    {
        return [
            'invalid_blank' => ['', null, PregException::class],
            'invalid_open' => ["\1", null, PregException::class, "'\1' found"],
            'valid_control_character_delimiter' => ["\1\1", 1],
            'invalid_control_character_modifier' => ["\1\1\1", null, PregException::class, ' Unknown modifier '],
            'valid_slate' => ['//', 1],
            'valid_paired' => ['()', 1],
            'null_byte_injection' => ['()'."\0", null, PregException::class, ' Null byte in regex '],
            'paired_non_utf8_only' => ["((*UTF8)\xFF)", null, PregException::class, 'UTF-8'],
            'valid_paired_non_utf8_only' => ["(\xFF)", 1],
            'php_version_dependent' => ['([\\R])', 0, PregException::class, 'Compilation failed: escape sequence is invalid '],
        ];
    }

    /**
     * @dataProvider providePatternValidationCases
     *
     * @param $pattern
     * @param null|int    $expected
     * @param null|string $expectedException
     * @param null|string $expectedMessage
     */
    public function testPatternValidation($pattern, $expected = null, $expectedException = null, $expectedMessage = null)
    {
        $setup = function () use ($expectedException, $expectedMessage) {
            $i = 0;

            if (null !== $expectedException) {
                ++$i;
                $this->expectException($expectedException);
            }

            if (null !== $expectedMessage) {
                ++$i;
                $this->expectExceptionMessage($expectedMessage);
            }

            return (bool) $i;
        };

        try {
            $actual = Preg::match($pattern, "The quick brown \xFF\x00\\xXX jumps over the lazy dog\n");
        } catch (\Exception $ex) {
            $setup();

            throw $ex;
        }

        if (null !== $expected) {
            static::assertSame($expected, $actual);

            return;
        }

        $setup() || $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider providePatternValidationCases
     *
     * @param string      $pattern
     * @param null|int    $expected
     * @param null|string $expectedException
     * @param null|string $expectedMessage
     */
    public function testPatternsValidation($pattern, $expected = null, $expectedException = null, $expectedMessage = null)
    {
        $setup = function () use ($expectedException, $expectedMessage) {
            $i = 0;

            if (null !== $expectedException) {
                ++$i;
                $this->expectException($expectedException);
            }

            if (null !== $expectedMessage) {
                ++$i;
                $this->expectExceptionMessage($expectedMessage);
            }

            return (bool) $i;
        };

        try {
            $buffer = "The quick brown \xFF\x00\\xXX jumps over the lazy dog\n";
            $actual = $buffer !== Preg::replace((array) $pattern, 'abc', $buffer);
        } catch (\Exception $ex) {
            $setup();

            throw $ex;
        }

        if (null !== $expected) {
            static::assertSame((bool) $expected, $actual);

            return;
        }

        $setup() || $this->addToAssertionCount(1);
    }

    public function testMatchAllFailing()
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::matchAll(): Invalid PCRE pattern ""');

        Preg::matchAll('', 'foo', $matches);
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     */
    public function testMatchAll($pattern, $subject)
    {
        $expectedResult = preg_match_all($pattern, $subject, $expectedMatches);
        $actualResult = Preg::matchAll($pattern, $subject, $actualMatches);

        static::assertSame($expectedResult, $actualResult);
        static::assertSame($expectedMatches, $actualMatches);
    }

    public function testReplaceFailing()
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessageRegExp('~\Q\Preg::replace()\E: Invalid PCRE pattern "": \(code: \d+\) [^(]+ \(version: \d+~');

        Preg::replace('', 'foo', 'bar');
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     * @dataProvider provideArrayOfPatternsCases
     */
    public function testReplace($pattern, $subject)
    {
        $expectedResult = preg_replace($pattern, 'foo', $subject);
        $actualResult = Preg::replace($pattern, 'foo', $subject);

        static::assertSame($expectedResult, $actualResult);
    }

    public function testReplaceCallbackFailing()
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::replaceCallback(): Invalid PCRE pattern ""');

        Preg::replaceCallback('', 'sort', 'foo');
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     * @dataProvider provideArrayOfPatternsCases
     */
    public function testReplaceCallback($pattern, $subject)
    {
        $callback = function (array $x) { return implode('-', $x); };

        $expectedResult = preg_replace_callback($pattern, $callback, $subject);
        $actualResult = Preg::replaceCallback($pattern, $callback, $subject);

        static::assertSame($expectedResult, $actualResult);
    }

    public function provideCommonCases()
    {
        return [
            ['/u/u', 'u'],
            ['/u/u', 'u/u'],
            ['/./', \chr(224).'bc'],
            ['/à/', 'àbc'],
            ['/'.\chr(224).'|í/', 'àbc'],
        ];
    }

    public function provideArrayOfPatternsCases()
    {
        return [
            [['/à/', '/í/'], 'Tàíl'],
            [['/'.\chr(174).'/', '/'.\chr(224).'/'], 'foo'],
        ];
    }

    public function testSplitFailing()
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::split(): Invalid PCRE pattern ""');

        Preg::split('', 'foo');
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @dataProvider provideCommonCases
     */
    public function testSplit($pattern, $subject)
    {
        $expectedResult = preg_split($pattern, $subject);
        $actualResult = Preg::split($pattern, $subject);

        static::assertSame($expectedResult, $actualResult);
    }

    public function testCorrectnessForUtf8String()
    {
        $pattern = '/./';
        $subject = 'àbc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        static::assertSame(['à'], $methodMatches);
        static::assertNotSame(['à'], $functionMatches);
    }

    public function testCorrectnessForNonUtf8String()
    {
        $pattern = '/./u';
        $subject = \chr(224).'bc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        static::assertSame([\chr(224)], $methodMatches);
        static::assertNotSame([\chr(224)], $functionMatches);
    }
}
