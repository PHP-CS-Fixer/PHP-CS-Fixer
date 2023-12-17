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
    public function testMatchFailing(): void
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::match(): Invalid PCRE pattern ""');

        Preg::match('', 'foo', $matches);
    }

    /**
     * @dataProvider provideCommonCases
     */
    public function testMatch(string $pattern, string $subject): void
    {
        $expectedResult = 1 === preg_match($pattern, $subject, $expectedMatches);
        $actualResult = Preg::match($pattern, $subject, $actualMatches);

        self::assertSame($expectedResult, $actualResult);
        self::assertSame($expectedMatches, $actualMatches);
    }

    public static function providePatternValidationCases(): iterable
    {
        yield 'invalid_blank' => ['', null, PregException::class];

        yield 'invalid_open' => ["\1", null, PregException::class, "/'\x01' found/"];

        yield 'valid_control_character_delimiter' => ["\1\1", true];

        yield 'invalid_control_character_modifier' => ["\1\1\1", null, PregException::class, '/ Unknown modifier /'];

        yield 'valid_slate' => ['//', true];

        yield 'valid_paired' => ['()', true];

        yield 'paired_non_utf8_only' => ["((*UTF8)\xFF)", null, PregException::class, '/UTF-8/'];

        yield 'valid_paired_non_utf8_only' => ["(\xFF)", true];

        yield 'php_version_dependent' => ['([\\R])', false, PregException::class, '/Compilation failed: escape sequence is invalid/'];

        yield 'null_byte_injection' => ['()'."\0", null, PregException::class, ' (NUL is not a valid modifier|Null byte in regex) '];
    }

    /**
     * @dataProvider providePatternValidationCases
     */
    public function testPatternValidation(string $pattern, ?bool $expected = null, ?string $expectedException = null, ?string $expectedMessage = null): void
    {
        $setup = function () use ($expectedException, $expectedMessage): bool {
            $i = 0;

            if (null !== $expectedException) {
                ++$i;
                $this->expectException($expectedException);
            }

            if (null !== $expectedMessage) {
                ++$i;
                $this->expectExceptionMessageMatches($expectedMessage);
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
            self::assertSame($expected, $actual);

            return;
        }

        if (!$setup()) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @dataProvider providePatternValidationCases
     */
    public function testPatternsValidation(string $pattern, ?bool $expected = null, ?string $expectedException = null, ?string $expectedMessage = null): void
    {
        $setup = function () use ($expectedException, $expectedMessage): bool {
            $i = 0;

            if (null !== $expectedException) {
                ++$i;
                $this->expectException($expectedException);
            }

            if (null !== $expectedMessage) {
                ++$i;
                $this->expectExceptionMessageMatches($expectedMessage);
            }

            return (bool) $i;
        };

        try {
            $buffer = "The quick brown \xFF\x00\\xXX jumps over the lazy dog\n";
            $actual = $buffer !== Preg::replace($pattern, 'abc', $buffer);
        } catch (\Exception $ex) {
            $setup();

            throw $ex;
        }

        if (null !== $expected) {
            self::assertSame($expected, $actual);

            return;
        }

        if (!$setup()) {
            $this->addToAssertionCount(1);
        }
    }

    public function testMatchAllFailing(): void
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::matchAll(): Invalid PCRE pattern ""');

        Preg::matchAll('', 'foo', $matches);
    }

    /**
     * @dataProvider provideCommonCases
     */
    public function testMatchAll(string $pattern, string $subject): void
    {
        $expectedResult = preg_match_all($pattern, $subject, $expectedMatches);
        $actualResult = Preg::matchAll($pattern, $subject, $actualMatches);

        self::assertSame($expectedResult, $actualResult);
        self::assertSame($expectedMatches, $actualMatches);
    }

    public function testReplaceFailing(): void
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessageMatches('~\Q\Preg::replace()\E: Invalid PCRE pattern "": \(code: \d+\) [^(]+ \(version: \d+~');

        Preg::replace('', 'foo', 'bar');
    }

    /**
     * @param string|string[] $pattern
     * @param string|string[] $subject
     *
     * @dataProvider provideCommonCases
     */
    public function testReplace($pattern, $subject): void
    {
        $expectedResult = preg_replace($pattern, 'foo', $subject);
        $actualResult = Preg::replace($pattern, 'foo', $subject);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testReplaceCallbackFailing(): void
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::replaceCallback(): Invalid PCRE pattern ""');

        Preg::replaceCallback('', 'sort', 'foo');
    }

    /**
     * @param string|string[] $pattern
     * @param string|string[] $subject
     *
     * @dataProvider provideCommonCases
     */
    public function testReplaceCallback($pattern, $subject): void
    {
        $callback = static fn (array $x): string => implode('-', $x);

        $expectedResult = preg_replace_callback($pattern, $callback, $subject);
        $actualResult = Preg::replaceCallback($pattern, $callback, $subject);

        self::assertSame($expectedResult, $actualResult);
    }

    public static function provideCommonCases(): iterable
    {
        yield ['/u/u', 'u'];

        yield ['/u/u', 'u/u'];

        yield ['/./', \chr(224).'bc'];

        yield ['/à/', 'àbc'];

        yield ['/'.\chr(224).'|í/', 'àbc'];
    }

    public function testSplitFailing(): void
    {
        $this->expectException(PregException::class);
        $this->expectExceptionMessage('Preg::split(): Invalid PCRE pattern ""');

        Preg::split('', 'foo');
    }

    /**
     * @dataProvider provideCommonCases
     */
    public function testSplit(string $pattern, string $subject): void
    {
        $expectedResult = preg_split($pattern, $subject);
        $actualResult = Preg::split($pattern, $subject);

        self::assertSame($expectedResult, $actualResult);
    }

    public function testCorrectnessForUtf8String(): void
    {
        $pattern = '/./';
        $subject = 'àbc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        self::assertSame(['à'], $methodMatches);
        self::assertNotSame(['à'], $functionMatches);
    }

    public function testCorrectnessForNonUtf8String(): void
    {
        $pattern = '/./u';
        $subject = \chr(224).'bc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        self::assertSame([\chr(224)], $methodMatches);
        self::assertNotSame([\chr(224)], $functionMatches);
    }
}
