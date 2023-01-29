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

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Utils;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Odín del Río <odin.drp@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Utils
 */
final class UtilsTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @var null|false|string
     */
    private $originalValueOfFutureMode;

    protected function setUp(): void
    {
        $this->originalValueOfFutureMode = getenv('PHP_CS_FIXER_FUTURE_MODE');
    }

    protected function tearDown(): void
    {
        putenv("PHP_CS_FIXER_FUTURE_MODE={$this->originalValueOfFutureMode}");
    }

    /**
     * @param string $expected Camel case string
     *
     * @dataProvider provideCamelCaseToUnderscoreCases
     */
    public function testCamelCaseToUnderscore(string $expected, string $input = null): void
    {
        if (null !== $input) {
            static::assertSame($expected, Utils::camelCaseToUnderscore($input));
        }

        static::assertSame($expected, Utils::camelCaseToUnderscore($expected));
    }

    public static function provideCamelCaseToUnderscoreCases(): array
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
            [
                'a',
                'A',
            ],
            [
                'aa',
                'AA',
            ],
            [
                'foo',
                'FOO',
            ],
            [
                'foo_bar_baz',
                'FooBarBAZ',
            ],
            [
                'foo_bar_baz',
                'FooBARBaz',
            ],
            [
                'foo_bar_baz',
                'FOOBarBaz',
            ],
            [
                'mr_t',
                'MrT',
            ],
            [
                'voyage_éclair',
                'VoyageÉclair',
            ],
        ];
    }

    /**
     * @param array{int, string}|string $input token prototype
     *
     * @dataProvider provideCalculateTrailingWhitespaceIndentCases
     */
    public function testCalculateTrailingWhitespaceIndent(string $spaces, $input): void
    {
        $token = new Token($input);

        static::assertSame($spaces, Utils::calculateTrailingWhitespaceIndent($token));
    }

    public static function provideCalculateTrailingWhitespaceIndentCases(): array
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

    public function testCalculateTrailingWhitespaceIndentFail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given token must be whitespace, got "T_STRING".');

        $token = new Token([T_STRING, 'foo']);

        Utils::calculateTrailingWhitespaceIndent($token);
    }

    /**
     * @param list<mixed> $expected
     * @param list<mixed> $elements
     *
     * @dataProvider provideStableSortCases
     */
    public function testStableSort(
        array $expected,
        array $elements,
        callable $getComparableValueCallback,
        callable $compareValuesCallback
    ): void {
        static::assertSame(
            $expected,
            Utils::stableSort($elements, $getComparableValueCallback, $compareValuesCallback)
        );
    }

    public static function provideStableSortCases(): array
    {
        return [
            [
                ['a', 'b', 'c', 'd', 'e'],
                ['b', 'd', 'e', 'a', 'c'],
                static fn ($element) => $element,
                'strcmp',
            ],
            [
                ['b', 'd', 'e', 'a', 'c'],
                ['b', 'd', 'e', 'a', 'c'],
                static fn (): string => 'foo',
                'strcmp',
            ],
            [
                ['b', 'd', 'e', 'a', 'c'],
                ['b', 'd', 'e', 'a', 'c'],
                static fn ($element) => $element,
                static fn (): int => 0,
            ],
            [
                ['bar1', 'baz1', 'foo1', 'bar2', 'baz2', 'foo2'],
                ['foo1', 'foo2', 'bar1', 'bar2', 'baz1', 'baz2'],
                static fn ($element) => preg_replace('/([a-z]+)(\d+)/', '$2$1', $element),
                'strcmp',
            ],
        ];
    }

    public function testSortFixers(): void
    {
        $fixers = [
            $this->createFixerDouble('f1', 0),
            $this->createFixerDouble('f2', -10),
            $this->createFixerDouble('f3', 10),
            $this->createFixerDouble('f4', -10),
        ];

        static::assertSame(
            [
                $fixers[2],
                $fixers[0],
                $fixers[1],
                $fixers[3],
            ],
            Utils::sortFixers($fixers)
        );
    }

    public function testNaturalLanguageJoinWithBackticksThrowsInvalidArgumentExceptionForEmptyArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Utils::naturalLanguageJoinWithBackticks([]);
    }

    /**
     * @param list<string> $names
     *
     * @dataProvider provideNaturalLanguageJoinWithBackticksCases
     */
    public function testNaturalLanguageJoinWithBackticks(string $joined, array $names): void
    {
        static::assertSame($joined, Utils::naturalLanguageJoinWithBackticks($names));
    }

    public static function provideNaturalLanguageJoinWithBackticksCases(): array
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

    /**
     * @group legacy
     */
    public function testTriggerDeprecationWhenFutureModeIsOff(): void
    {
        putenv('PHP_CS_FIXER_FUTURE_MODE=0');

        $message = __METHOD__.'::The message';
        $this->expectDeprecation($message);

        Utils::triggerDeprecation(new \DomainException($message));

        $triggered = Utils::getTriggeredDeprecations();
        static::assertContains($message, $triggered);
    }

    public function testTriggerDeprecationWhenFutureModeIsOn(): void
    {
        putenv('PHP_CS_FIXER_FUTURE_MODE=1');

        $message = __METHOD__.'::The message';
        $exception = new \DomainException($message);
        $futureModeException = null;

        try {
            Utils::triggerDeprecation($exception);
        } catch (\Exception $futureModeException) {
        }

        static::assertInstanceOf(\RuntimeException::class, $futureModeException);
        static::assertSame($exception, $futureModeException->getPrevious());

        $triggered = Utils::getTriggeredDeprecations();
        static::assertNotContains($message, $triggered);
    }

    private function createFixerDouble(string $name, int $priority): FixerInterface
    {
        $fixer = $this->prophesize(FixerInterface::class);
        $fixer->getName()->willReturn($name);
        $fixer->getPriority()->willReturn($priority);

        return $fixer->reveal();
    }
}
