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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\OrderedTypesFixer
 */
final class OrderedTypesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param null|array<string, string> $config
     */
    public function testFix(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, (null|array<string, string>|string)[]|string[]>
     */
    public static function provideFixCases(): iterable
    {
        yield 'catch with default, no spaces, with both leading slash' => [
            '<?php
try {
    $this->foo();
} catch (\LogicException|\RuntimeException $e) {
    // $e
}
',
            '<?php
try {
    $this->foo();
} catch (\RuntimeException|\LogicException $e) {
    // $e
}
',
        ];

        yield 'catch with default, with spaces, with both leading slash' => [
            '<?php
try {
    $this->foo();
} catch (\LogicException|\RuntimeException $e) {
    // $e
}
',
            '<?php
try {
    $this->foo();
} catch (\RuntimeException | \LogicException $e) {
    // $e
}
',
        ];

        yield 'catch with default, no spaces, with no leading slash' => [
            '<?php
try {
    cache()->save();
} catch (CacheException|SimpleCacheException $e) {
    // $e
}
',
            '<?php
try {
    cache()->save();
} catch (SimpleCacheException|CacheException $e) {
    // $e
}
',
        ];

        yield 'catch with default, with spaces, with one leading slash' => [
            '<?php
try {
    cache()->save();
} catch (CacheException|\RuntimeException $e) {
    // $e
}
',
            '<?php
try {
    cache()->save();
} catch (\RuntimeException | CacheException $e) {
    // $e
}
',
        ];

        yield 'catch with no sorting' => [
            '<?php
try {
    $this->foo();
} catch (\RuntimeException|\LogicException $e) {
    // $e
}
',
            null,
            ['sort_algorithm' => 'none'],
        ];

        yield 'nothing to fix' => [
            '<?php
try {
    $this->foo();
} catch (\LogicException $e) {
    // $e
}
',
        ];

        yield 'already fixed' => [
            '<?php
try {
    $this->foo();
} catch (LogicException|RuntimeException $e) {
    // $e
}
',
        ];
    }

    /**
     * @dataProvider provideFixPhp80Cases
     *
     * @param null|array<string, string> $config
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|array<string, string>|string)[]>
     */
    public static function provideFixPhp80Cases(): iterable
    {
        yield 'sort alpha, null none' => [
            "<?php\nclass Foo\n{\n    public A|null|Z \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public Z|null|A \$bar;\n}\n",
            ['sort_algorithm' => 'alpha', 'null_adjustment' => 'none'],
        ];

        yield 'sort alpha, null first' => [
            "<?php\nclass Foo\n{\n    public null|A|Z \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public Z|null|A \$bar;\n}\n",
            ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_first'],
        ];

        yield 'sort alpha, null last' => [
            "<?php\nclass Foo\n{\n    public A|Z|null \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public Z|null|A \$bar;\n}\n",
            ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last'],
        ];

        yield 'sort none, null first' => [
            "<?php\nclass Foo\n{\n    public null|Z|A \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public Z|null|A \$bar;\n}\n",
            ['sort_algorithm' => 'none', 'null_adjustment' => 'always_first'],
        ];

        yield 'sort none, null last' => [
            "<?php\nclass Foo\n{\n    public Z|A|null \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public Z|null|A \$bar;\n}\n",
            ['sort_algorithm' => 'none', 'null_adjustment' => 'always_last'],
        ];

        yield 'sort none, null none' => [
            "<?php\nclass Foo\n{\n    public Z|null|A \$bar;\n}\n",
            null,
            ['sort_algorithm' => 'none', 'null_adjustment' => 'none'],
        ];
    }

    /**
     * @dataProvider provideFixDefaultCases
     *
     * @requires PHP 8.0
     */
    public function testFixDefault(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|array<string, string>|string)[]|string[]>
     */
    public static function provideFixDefaultCases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\n    public null|int|string \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|null|int \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public null|A|B \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public B|A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public null|\\A|B \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public B|\\A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public null|\\A|\\B \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public \\B|\\A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public null|int|string/* array */ \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|null|int/* array */ \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public /* int */null|A|B \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public /* int */B|A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public     null|A|B     \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public     B|A|null     \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(null|array|callable|int \$cb) {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(array|int|callable|null \$cb) {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(\$cb): null|array|callable|int {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(\$cb): array|int|callable|null {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(\$cb): null|static {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(\$cb): static|null {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public function bar(null|string \$str, null|array|int \$arr) {}\n}\n",
            "<?php\nclass Foo\n{\n    public function bar(string|null \$str, int|array|null \$arr) {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public function bar(\\JsonSerializable|\\Stringable \$obj): array|int {}\n}\n",
            "<?php\nclass Foo\n{\n    public function bar(\\Stringable|\\JsonSerializable \$obj): int|array {}\n}\n",
        ];

        yield [
            '<?php function foo(null|int|string $bar): null|\Stringable {}',
            '<?php function foo(string|int|null $bar): \Stringable|null {}',
        ];

        yield [
            '<?php fn (null|\Countable|int $number): null|int => $number;',
            '<?php fn (int|\Countable|null $number): int|null => $number;',
        ];

        yield [
            "<?php\ntry {\n    foo();\n} catch (\\Error|\\TypeError) {\n}\n",
            "<?php\ntry {\n    foo();\n} catch (\\TypeError|\\Error) {\n}\n",
        ];

        yield [
            '<?php
class Foo
{
    public ?string $foo = null;
    public /* int|string */ $bar;
    private null|array $baz = null;

    public function baz(): null|string {}
    protected function bar(string $str, ?array $config = null): callable {}
}

try {
    (new Foo)->baz();
} catch (Exception $e) {
    return $e;
}
',
        ];
    }

    /**
     * @dataProvider provideFixWithAlphaAlgorithmAndNullAlwaysLastCases
     *
     * @requires PHP 8.0
     */
    public function testFixWithAlphaAlgorithmAndNullAlwaysLast(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'always_last',
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|string|string[])[]|string[]>
     */
    public static function provideFixWithAlphaAlgorithmAndNullAlwaysLastCases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\n    public int|string|null \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|null|int \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public A|B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public B|A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public \\A|B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public B|\\A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public \\A|\\B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public \\B|\\A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public int|string|null/* array */ \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|null|int/* array */ \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public /* int */A|B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public /* int */B|A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public     A|B|null     \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public     B|A|null     \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(array|callable|int|null \$cb) {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(array|int|callable|null \$cb) {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(\$cb): array|callable|int|null {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(\$cb): array|int|callable|null {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(\$cb): static|null {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(\$cb): null|static {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public function bar(string|null \$str, array|int|null \$arr) {}\n}\n",
            "<?php\nclass Foo\n{\n    public function bar(string|null \$str, int|array|null \$arr) {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public function bar(\\JsonSerializable|\\Stringable \$obj): array|int {}\n}\n",
            "<?php\nclass Foo\n{\n    public function bar(\\Stringable|\\JsonSerializable \$obj): int|array {}\n}\n",
        ];

        yield [
            '<?php function foo(int|string|null $bar): \Stringable|null {}',
            '<?php function foo(string|int|null $bar): \Stringable|null {}',
        ];

        yield [
            '<?php fn (\Countable|int|null $number): int|null => $number;',
            '<?php fn (int|\Countable|null $number): int|null => $number;',
        ];

        yield [
            "<?php\ntry {\n    foo();\n} catch (\\Error|\\TypeError) {\n}\n",
            "<?php\ntry {\n    foo();\n} catch (\\TypeError|\\Error) {\n}\n",
        ];

        yield [
            '<?php
class Foo
{
    public ?string $foo = null;
    public /* int|string */ $bar;
    private array|null $baz = null;

    public function baz(): string|null {}
    protected function bar(string $str, ?array $config = null): callable {}
}

try {
    (new Foo)->baz();
} catch (Exception $e) {
    return $e;
}
',
        ];
    }

    /**
     * @dataProvider provideFixWithAlphaAlgorithmOnlyCases
     *
     * @requires PHP 8.0
     */
    public function testFixWithAlphaAlgorithmOnly(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'none',
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|array<string, string>|string)[]|string[]>
     */
    public static function provideFixWithAlphaAlgorithmOnlyCases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\n    public int|null|string \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|null|int \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public A|B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public B|A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public \\A|B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public B|\\A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public \\A|\\B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public \\B|\\A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public int|null|string/* array */ \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|null|int/* array */ \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public /* int */A|B|null \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public /* int */B|A|null \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public     A|B|null     \$foo = null;\n}\n",
            "<?php\nclass Foo\n{\n    public     B|A|null     \$foo = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(array|callable|int|null \$cb) {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(array|int|callable|null \$cb) {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(\$cb): array|callable|int|null {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(\$cb): array|int|callable|null {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(\$cb): null|static {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(\$cb): static|null {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public function bar(null|string \$str, array|int|null \$arr) {}\n}\n",
            "<?php\nclass Foo\n{\n    public function bar(string|null \$str, int|array|null \$arr) {}\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public function bar(\\JsonSerializable|\\Stringable \$obj): array|int {}\n}\n",
            "<?php\nclass Foo\n{\n    public function bar(\\Stringable|\\JsonSerializable \$obj): int|array {}\n}\n",
        ];

        yield [
            '<?php function foo(int|null|string $bar): null|\Stringable {}',
            '<?php function foo(string|int|null $bar): \Stringable|null {}',
        ];

        yield [
            '<?php fn (\Countable|int|null $number): int|null => $number;',
            '<?php fn (int|\Countable|null $number): int|null => $number;',
        ];

        yield [
            "<?php\ntry {\n    foo();\n} catch (\\Error|\\TypeError) {\n}\n",
            "<?php\ntry {\n    foo();\n} catch (\\TypeError|\\Error) {\n}\n",
        ];

        yield [
            '<?php
class Foo
{
    public ?string $foo = null;
    public /* int|string */ $bar;
    private array|null $baz = null;

    public function baz(): null|string {}
    protected function bar(string $str, ?array $config = null): callable {}
}

try {
    (new Foo)->baz();
} catch (Exception $e) {
    return $e;
}
',
        ];
    }

    /**
     * @dataProvider provideFixWithSandwichedWhitespaceOrCommentInTypeCases
     *
     * @requires PHP 8.0
     */
    public function testFixWithSandwichedWhitespaceOrCommentInType(string $expected, ?string $input = null): void
    {
        // The current design of the fixer uses the `TypeAnalysis` class which collects the
        // types and ignores whitespaces and comments. These ignored tokens are not accounted
        // during instantiation of the TypeAnalysis class, thus we have no way of recovering
        // those token information. This test case mainly proves this fact.
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|array<string, string>|string)[]|string[]>
     */
    public static function provideFixWithSandwichedWhitespaceOrCommentInTypeCases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\n    public null|int|string \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string   |   null   |   int \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public null|int|string \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string | null | int \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public null|int|string \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string |/* array| */null|int \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    private function bar(\$cb): null|static {}\n}\n",
            "<?php\nclass Foo\n{\n    private function bar(\$cb): static /* |int */ | null {}\n}\n",
        ];
    }

    /**
     * @dataProvider provideFixPhp81Cases
     *
     * @param null|array<string, string> $config
     *
     * @requires PHP 8.1
     */
    public function testFixPhp81(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|array<string, string>|string)[]|string[]>
     */
    public static function provideFixPhp81Cases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\n    public A&B \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public B&A \$bar;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public Ae&At \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public At&Ae \$bar;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public readonly null|A|B \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public readonly B|null|A \$bar;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public readonly A|B|null \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public readonly B|null|A \$bar;\n}\n",
            ['null_adjustment' => 'always_last'],
        ];

        yield [
            "<?php\nclass Foo\n{\n    public readonly A|null|X \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public readonly X|A|null \$bar;\n}\n",
            ['null_adjustment' => 'none'],
        ];

        yield [
            "<?php\nclass Foo\n{\n    public B&A \$bar;\n}\n",
            null,
            ['sort_algorithm' => 'none'],
        ];
    }

    /**
     * Provisional support for PHP 8.2's Disjunctive Normal Form (DNF) Types.
     *
     * @dataProvider provideFixPhp82Cases
     *
     * @param null|array<string, string> $config
     *
     * @requires PHP 8.2
     */
    public function testFixPhp82(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|array<string, string>|string)[]|string[]>
     */
    public static function provideFixPhp82Cases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\n    public null|array|(At&Bz)|string \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|(Bz&At)|array|null \$bar = null;\n}\n",
        ];

        yield [
            "<?php\nclass Foo\n{\n    public array|(At&Bz)|string|null \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|(Bz&At)|array|null \$bar = null;\n}\n",
            ['null_adjustment' => 'always_last'],
        ];

        yield [
            "<?php\nclass Foo\n{\n    public array|(At&Bz)|null|string \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public string|(Bz&At)|array|null \$bar = null;\n}\n",
            ['null_adjustment' => 'none'],
        ];

        yield [
            "<?php\nclass Foo\n{\n    public (A&B)|(A&C)|(B&D)|(C&D) \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public (D&B)|(C&A)|(B&A)|(D&C) \$bar;\n}\n",
            ['sort_algorithm' => 'alpha'],
        ];

        yield [
            "<?php\nclass Foo\n{\n    public (A&B)|(\\A&C)|(B&\\D)|(C&D) \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public (\\D&B)|(C&\\A)|(B&A)|(D&C) \$bar;\n}\n",
            ['sort_algorithm' => 'alpha'],
        ];

        yield [
            "<?php\nclass Foo\n{\n    public (A&C)|(B&D) \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public (D&B)|(C&A) \$bar;\n}\n",
            ['sort_algorithm' => 'alpha'],
        ];

        yield [
            "<?php\nclass Foo\n{\n    public (\\A&\\C)|(\\B&\\D) \$bar;\n}\n",
            "<?php\nclass Foo\n{\n    public (\\D&\\B)|(\\C&\\A) \$bar;\n}\n",
            ['sort_algorithm' => 'alpha'],
        ];
    }

    /**
     * @dataProvider provideFixWithCaseSensitiveCases
     *
     * @requires PHP 8.0
     */
    public function testFixWithCaseSensitive(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'case_sensitive' => true,
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<(null|array<string, string>|string)[]|string[]>
     */
    public static function provideFixWithCaseSensitiveCases(): iterable
    {
        yield [
            "<?php\nclass Foo\n{\n    public null|AAa|Aa \$bar = null;\n}\n",
            "<?php\nclass Foo\n{\n    public Aa|AAa|null \$bar = null;\n}\n",
        ];
    }
}
