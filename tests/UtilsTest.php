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
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

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
            self::assertSame($expected, Utils::camelCaseToUnderscore($input));
        }

        self::assertSame($expected, Utils::camelCaseToUnderscore($expected));
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideCamelCaseToUnderscoreCases(): iterable
    {
        yield [
            'dollar_close_curly_braces',
            'DollarCloseCurlyBraces',
        ];

        yield [
            'utf8_encoder_fixer',
            'utf8EncoderFixer',
        ];

        yield [
            'terminated_with_number10',
            'TerminatedWithNumber10',
        ];

        yield [
            'utf8_encoder_fixer',
        ];

        yield [
            'a',
            'A',
        ];

        yield [
            'aa',
            'AA',
        ];

        yield [
            'foo',
            'FOO',
        ];

        yield [
            'foo_bar_baz',
            'FooBarBAZ',
        ];

        yield [
            'foo_bar_baz',
            'FooBARBaz',
        ];

        yield [
            'foo_bar_baz',
            'FOOBarBaz',
        ];

        yield [
            'mr_t',
            'MrT',
        ];

        yield [
            'voyage_éclair',
            'VoyageÉclair',
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

        self::assertSame($spaces, Utils::calculateTrailingWhitespaceIndent($token));
    }

    /**
     * @return iterable<array{string, array{int, string}|string}>
     */
    public static function provideCalculateTrailingWhitespaceIndentCases(): iterable
    {
        yield ['    ', [T_WHITESPACE, "\n\n    "]];

        yield [' ', [T_WHITESPACE, "\r\n\r\r\r "]];

        yield ["\t", [T_WHITESPACE, "\r\n\t"]];

        yield ['', [T_WHITESPACE, "\t\n\r"]];

        yield ['', [T_WHITESPACE, "\n"]];

        yield ['', ''];
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
        self::assertSame(
            $expected,
            Utils::stableSort($elements, $getComparableValueCallback, $compareValuesCallback)
        );
    }

    /**
     * @return iterable<array{list<mixed>, list<mixed>, callable, callable}>
     */
    public static function provideStableSortCases(): iterable
    {
        yield [
            ['a', 'b', 'c', 'd', 'e'],
            ['b', 'd', 'e', 'a', 'c'],
            static fn ($element) => $element,
            'strcmp',
        ];

        yield [
            ['b', 'd', 'e', 'a', 'c'],
            ['b', 'd', 'e', 'a', 'c'],
            static fn (): string => 'foo',
            'strcmp',
        ];

        yield [
            ['b', 'd', 'e', 'a', 'c'],
            ['b', 'd', 'e', 'a', 'c'],
            static fn ($element) => $element,
            static fn (): int => 0,
        ];

        yield [
            ['bar1', 'baz1', 'foo1', 'bar2', 'baz2', 'foo2'],
            ['foo1', 'foo2', 'bar1', 'bar2', 'baz1', 'baz2'],
            static fn ($element) => preg_replace('/([a-z]+)(\d+)/', '$2$1', $element),
            'strcmp',
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

        self::assertSame(
            [
                $fixers[2],
                $fixers[0],
                $fixers[1],
                $fixers[3],
            ],
            Utils::sortFixers($fixers)
        );
    }

    public function testNaturalLanguageJoinThrowsInvalidArgumentExceptionForEmptyArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Array of names cannot be empty.');

        Utils::naturalLanguageJoin([]);
    }

    public function testNaturalLanguageJoinThrowsInvalidArgumentExceptionForMoreThanOneCharWrapper(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Wrapper should be a single-char string or empty.');

        Utils::naturalLanguageJoin(['a', 'b'], 'foo');
    }

    /**
     * @dataProvider provideNaturalLanguageJoinCases
     *
     * @param list<string> $names
     */
    public function testNaturalLanguageJoin(string $joined, array $names, string $wrapper = '"'): void
    {
        self::assertSame($joined, Utils::naturalLanguageJoin($names, $wrapper));
    }

    /**
     * @return iterable<array{0: string, 1: list<string>, 2?: string}>
     */
    public static function provideNaturalLanguageJoinCases(): iterable
    {
        yield [
            '"a"',
            ['a'],
        ];

        yield [
            '"a" and "b"',
            ['a', 'b'],
        ];

        yield [
            '"a", "b" and "c"',
            ['a', 'b', 'c'],
        ];

        yield [
            '\'a\'',
            ['a'],
            '\'',
        ];

        yield [
            '\'a\' and \'b\'',
            ['a', 'b'],
            '\'',
        ];

        yield [
            '\'a\', \'b\' and \'c\'',
            ['a', 'b', 'c'],
            '\'',
        ];

        yield [
            '?a?',
            ['a'],
            '?',
        ];

        yield [
            '?a? and ?b?',
            ['a', 'b'],
            '?',
        ];

        yield [
            '?a?, ?b? and ?c?',
            ['a', 'b', 'c'],
            '?',
        ];

        yield [
            'a',
            ['a'],
            '',
        ];

        yield [
            'a and b',
            ['a', 'b'],
            '',
        ];

        yield [
            'a, b and c',
            ['a', 'b', 'c'],
            '',
        ];
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
        self::assertSame($joined, Utils::naturalLanguageJoinWithBackticks($names));
    }

    /**
     * @return iterable<array{string, list<string>}>
     */
    public static function provideNaturalLanguageJoinWithBackticksCases(): iterable
    {
        yield [
            '`a`',
            ['a'],
        ];

        yield [
            '`a` and `b`',
            ['a', 'b'],
        ];

        yield [
            '`a`, `b` and `c`',
            ['a', 'b', 'c'],
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
        self::assertContains($message, $triggered);
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

        self::assertInstanceOf(\RuntimeException::class, $futureModeException);
        self::assertSame($exception, $futureModeException->getPrevious());

        $triggered = Utils::getTriggeredDeprecations();
        self::assertNotContains($message, $triggered);
    }

    /**
     * @param mixed $input
     *
     * @dataProvider provideToStringCases
     */
    public function testToString(string $expected, $input): void
    {
        self::assertSame($expected, Utils::toString($input));
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function provideToStringCases(): iterable
    {
        yield ["['a' => 3, 'b' => 'c']", ['a' => 3, 'b' => 'c']];

        yield ['[[1], [2]]', [[1], [2]]];

        yield ['[0 => [1], \'a\' => [2]]', [[1], 'a' => [2]]];

        yield ['[1, 2, \'foo\', null]', [1, 2, 'foo', null]];

        yield ['[1, 2]', [1, 2]];

        yield ['[]', []];

        yield ['1.5', 1.5];

        yield ['false', false];

        yield ['true', true];

        yield ['1', 1];

        yield ["'foo'", 'foo'];
    }

    private function createFixerDouble(string $name, int $priority): FixerInterface
    {
        return new class($name, $priority) implements FixerInterface {
            private string $name;
            private int $priority;

            public function __construct(string $name, int $priority)
            {
                $this->name = $name;
                $this->priority = $priority;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
