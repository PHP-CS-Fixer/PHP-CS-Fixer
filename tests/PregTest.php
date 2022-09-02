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
        $expectedResult = preg_match($pattern, $subject, $expectedMatches);
        $actualResult = Preg::match($pattern, $subject, $actualMatches);

        static::assertSame($expectedResult, $actualResult);
        static::assertSame($expectedMatches, $actualMatches);
    }

    public function providePatternValidationCases(): iterable
    {
        yield from [
            'invalid_blank' => ['', null, PregException::class],
            'invalid_open' => ["\1", null, PregException::class, "'\1' found"],
            'valid_control_character_delimiter' => ["\1\1", 1],
            'invalid_control_character_modifier' => ["\1\1\1", null, PregException::class, ' Unknown modifier '],
            'valid_slate' => ['//', 1],
            'valid_paired' => ['()', 1],
            'paired_non_utf8_only' => ["((*UTF8)\xFF)", null, PregException::class, 'UTF-8'],
            'valid_paired_non_utf8_only' => ["(\xFF)", 1],
            'php_version_dependent' => ['([\\R])', 0, PregException::class, 'Compilation failed: escape sequence is invalid '],
        ];

        $nullByteMessage = \PHP_VERSION_ID >= 80200 ? 'NUL is not a valid modifier' : 'Null byte in regex';

        yield 'null_byte_injection' => ['()'."\0", null, PregException::class, " {$nullByteMessage} "];
    }

    /**
     * @dataProvider providePatternValidationCases
     */
    public function testPatternValidation(string $pattern, ?int $expected = null, ?string $expectedException = null, ?string $expectedMessage = null): void
    {
        $setup = function () use ($expectedException, $expectedMessage): bool {
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

        if (!$setup()) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * @dataProvider providePatternValidationCases
     */
    public function testPatternsValidation(string $pattern, ?int $expected = null, ?string $expectedException = null, ?string $expectedMessage = null): void
    {
        $setup = function () use ($expectedException, $expectedMessage): bool {
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
            $actual = $buffer !== Preg::replace($pattern, 'abc', $buffer);
        } catch (\Exception $ex) {
            $setup();

            throw $ex;
        }

        if (null !== $expected) {
            static::assertSame((bool) $expected, $actual);

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

        static::assertSame($expectedResult, $actualResult);
        static::assertSame($expectedMatches, $actualMatches);
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

        static::assertSame($expectedResult, $actualResult);
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

        static::assertSame($expectedResult, $actualResult);
    }

    public function provideCommonCases(): array
    {
        return [
            ['/u/u', 'u'],
            ['/u/u', 'u/u'],
            ['/./', \chr(224).'bc'],
            ['/à/', 'àbc'],
            ['/'.\chr(224).'|í/', 'àbc'],
        ];
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

        static::assertSame($expectedResult, $actualResult);
    }

    public function testCorrectnessForUtf8String(): void
    {
        $pattern = '/./';
        $subject = 'àbc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        static::assertSame(['à'], $methodMatches);
        static::assertNotSame(['à'], $functionMatches);
    }

    public function testCorrectnessForNonUtf8String(): void
    {
        $pattern = '/./u';
        $subject = \chr(224).'bc';

        Preg::match($pattern, $subject, $methodMatches);
        preg_match($pattern, $subject, $functionMatches);

        static::assertSame([\chr(224)], $methodMatches);
        static::assertNotSame([\chr(224)], $functionMatches);
    }
}
