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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer
 */
final class PhpdocTypesOrderFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixWithAlphaAlgorithmAndNullAlwaysFirstCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixWithNullFirstCases
     */
    public function testFixWithNullFirst(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'none',
            'null_adjustment' => 'always_first',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithNullFirstCases(): iterable
    {
        yield [
            '<?php /** @var null|string */',
            '<?php /** @var string|null */',
        ];

        yield [
            '<?php /** @param null|string $foo */',
            '<?php /** @param string|null $foo */',
        ];

        yield [
            '<?php /** @property null|string $foo */',
            '<?php /** @property string|null $foo */',
        ];

        yield [
            '<?php /** @property-read null|string $foo */',
            '<?php /** @property-read string|null $foo */',
        ];

        yield [
            '<?php /** @property-write null|string $foo */',
            '<?php /** @property-write string|null $foo */',
        ];

        yield [
            '<?php /** @method null|string foo(null|int $foo, null|string $bar) */',
            '<?php /** @method string|null foo(int|null $foo, string|null $bar) */',
        ];

        yield [
            '<?php /** @return null|string */',
            '<?php /** @return string|null */',
        ];

        yield [
            '<?php /** @var null|string[]|resource|false|object|Foo|Bar\Baz|bool[]|string|array|int */',
            '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
        ];

        yield [
            '<?php /** @var null|array<int, string> Foo */',
            '<?php /** @var array<int, string>|null Foo */',
        ];

        yield [
            '<?php /** @var null|array<int, array<string>> Foo */',
            '<?php /** @var array<int, array<string>>|null Foo */',
        ];

        yield [
            '<?php /** @var NULL|string */',
            '<?php /** @var string|NULL */',
        ];

        yield [
            '<?php /** @var Foo|?Bar */',
        ];

        yield [
            '<?php /** @var ?Foo|Bar */',
        ];

        yield [
            '<?php /** @var array<null|string> */',
            '<?php /** @var array<string|null> */',
        ];

        yield [
            '<?php /** @var array<int, null|string> */',
            '<?php /** @var array<int, string|null> */',
        ];

        yield [
            '<?php /** @var array<int,     array<null|int|string>> */',
            '<?php /** @var array<int,     array<int|string|null>> */',
        ];

        yield [
            '<?php /** @var null|null */',
        ];

        yield [
            '<?php /** @var null|\null */',
        ];

        yield [
            '<?php /** @var \null|null */',
        ];

        yield [
            '<?php /** @var \null|\null */',
        ];

        yield [
            '<?php /** @var \null|int */',
            '<?php /** @var int|\null */',
        ];

        yield [
            '<?php /** @var array<\null|int> */',
            '<?php /** @var array<int|\null> */',
        ];

        yield [
            '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
            '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
        ];

        yield [
            '<?php /** @var null|Foo[]|Foo|Foo\Bar|Foo_Bar */',
            '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
        ];

        yield [
            '<?php /** @method null|Y|X setOrder(array<string, null|array{Y,X,null|Z}> $by) */',
            '<?php /** @method Y|null|X setOrder(array<string, array{Y,X,Z|null}|null> $by) */',
        ];

        yield '@method with invalid 2nd phpdoc' => [
            '<?php /** @method null|X setOrder($$by) */',
            '<?php /** @method X|null setOrder($$by) */',
        ];

        yield [
            '<?php /** @var array<array<int, int>, OutputInterface> */',
        ];

        yield [
            '<?php /** @var iterable<array{names:array<string>, surname:string}> */',
        ];

        yield [
            '<?php /** @var iterable<array{surname:string, names:array<string>}> */',
        ];

        yield [
            '<?php /** @return array<array{level:string, message:string, context:array<mixed>}> */',
        ];

        yield [
            '<?php /** @return Data<array{enabled: string[], all: array<string, string>}> */',
        ];

        yield [
            '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
        ];

        yield [
            '<?php /** @param null|callable(array<string>): array<string, T> $callback */',
        ];

        yield [
            '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
        ];

        yield [
            '<?php /** @return Closure(Iterator<TKey, T>): Generator<int, array<TKey, T>> */',
        ];

        yield [
            '<?php /** @var Closure(Iterator<TKey, T>): Generator<int, array<TKey, T>> $pipe */',
        ];

        yield [
            '<?php /** @return Generator<int, Promise<mixed>, mixed, Identity> */',
        ];

        yield [
            '<?php /** @param null|callable(null|foo, null|bar): array<string, T> $callback */',
            '<?php /** @param null|callable(foo|null, bar|null): array<string, T> $callback */',
        ];

        yield [
            '<?php /** @param null|string$foo */',
            '<?php /** @param string|null$foo */',
        ];
    }

    /**
     * @dataProvider provideFixWithNullLastCases
     */
    public function testFixWithNullLast(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'none',
            'null_adjustment' => 'always_last',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithNullLastCases(): iterable
    {
        yield [
            '<?php /** @var string|null */',
            '<?php /** @var null|string */',
        ];

        yield [
            '<?php /** @param string|null $foo */',
            '<?php /** @param null|string $foo */',
        ];

        yield [
            '<?php /** @property string|null $foo */',
            '<?php /** @property null|string $foo */',
        ];

        yield [
            '<?php /** @property-read string|null $foo */',
            '<?php /** @property-read null|string $foo */',
        ];

        yield [
            '<?php /** @property-write string|null $foo */',
            '<?php /** @property-write null|string $foo */',
        ];

        yield [
            '<?php /** @method string|null foo(int|null $foo, string|null $bar) */',
            '<?php /** @method null|string foo(null|int $foo, null|string $bar) */',
        ];

        yield [
            '<?php /** @return string|null */',
            '<?php /** @return null|string */',
        ];

        yield [
            '<?php /** @var string[]|resource|false|object|Foo|Bar\Baz|bool[]|string|array|int|null */',
            '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
        ];

        yield [
            '<?php /** @var array<int, string>|null Foo */',
            '<?php /** @var null|array<int, string> Foo */',
        ];

        yield [
            '<?php /** @var array<int, array<string>>|null Foo */',
            '<?php /** @var null|array<int, array<string>> Foo */',
        ];

        yield [
            '<?php /** @var string|NULL */',
            '<?php /** @var NULL|string */',
        ];

        yield [
            '<?php /** @var Foo|?Bar */',
        ];

        yield [
            '<?php /** @var ?Foo|Bar */',
        ];

        yield [
            '<?php /** @var Foo|?\Bar */',
        ];

        yield [
            '<?php /** @var ?\Foo|Bar */',
        ];

        yield [
            '<?php /** @var array<string|null> */',
            '<?php /** @var array<null|string> */',
        ];

        yield [
            '<?php /** @var array<int, string|null> */',
            '<?php /** @var array<int, null|string> */',
        ];

        yield [
            '<?php /** @var array<int,     array<int|string|null>> */',
            '<?php /** @var array<int,     array<null|int|string>> */',
        ];

        yield [
            '<?php /** @var null|null */',
        ];

        yield [
            '<?php /** @var null|\null */',
        ];

        yield [
            '<?php /** @var \null|null */',
        ];

        yield [
            '<?php /** @var \null|\null */',
        ];

        yield [
            '<?php /** @var int|\null */',
            '<?php /** @var \null|int */',
        ];

        yield [
            '<?php /** @var array<int|\null> */',
            '<?php /** @var array<\null|int> */',
        ];

        yield [
            '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
        ];

        yield [
            '<?php /** @var Foo[]|Foo|Foo\Bar|Foo_Bar|null */',
            '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
        ];

        yield [
            '<?php /** @return array<int, callable(array<string, string|null> , DateTime): bool> */',
            '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
        ];
    }

    /**
     * @dataProvider provideFixWithAlphaAlgorithmCases
     */
    public function testFixWithAlphaAlgorithm(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'none',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithAlphaAlgorithmCases(): iterable
    {
        yield [
            '<?php /** @var int|null|string */',
            '<?php /** @var string|int|null */',
        ];

        yield [
            '<?php /** @param Bar|\Foo */',
            '<?php /** @param \Foo|Bar */',
        ];

        yield [
            '<?php /** @property-read \Bar|Foo */',
            '<?php /** @property-read Foo|\Bar */',
        ];

        yield [
            '<?php /** @property-write bar|Foo */',
            '<?php /** @property-write Foo|bar */',
        ];

        yield [
            '<?php /** @return Bar|foo */',
            '<?php /** @return foo|Bar */',
        ];

        yield [
            '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
            '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
        ];

        yield [
            '<?php /** @var array|Bar\Baz|bool[]|false|Foo|int|null|object|resource|string|string[] */',
            '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
        ];

        yield [
            '<?php /** @var array<int, string>|null Foo */',
            '<?php /** @var null|array<int, string> Foo */',
        ];

        yield [
            '<?php /** @var array<int, array<string>>|null Foo */',
            '<?php /** @var null|array<int, array<string>> Foo */',
        ];

        yield [
            '<?php /** @var ?Bar|Foo */',
            '<?php /** @var Foo|?Bar */',
        ];

        yield [
            '<?php /** @var Bar|?Foo */',
            '<?php /** @var ?Foo|Bar */',
        ];

        yield [
            '<?php /** @var ?\Bar|Foo */',
            '<?php /** @var Foo|?\Bar */',
        ];

        yield [
            '<?php /** @var Bar|?\Foo */',
            '<?php /** @var ?\Foo|Bar */',
        ];

        yield [
            '<?php /** @var array<null|string> */',
            '<?php /** @var array<string|null> */',
        ];

        yield [
            '<?php /** @var array<int|string, null|string> */',
            '<?php /** @var array<string|int, string|null> */',
        ];

        yield [
            '<?php /** @var array<int|string,     array<int|string, null|string>> */',
            '<?php /** @var array<string|int,     array<string|int, string|null>> */',
        ];

        yield [
            '<?php /** @var null|null */',
        ];

        yield [
            '<?php /** @var null|\null */',
        ];

        yield [
            '<?php /** @var \null|null */',
        ];

        yield [
            '<?php /** @var \null|\null */',
        ];

        yield [
            '<?php /** @var int|\null|string */',
            '<?php /** @var string|\null|int */',
        ];

        yield [
            '<?php /** @var array<int|\null|string> */',
            '<?php /** @var array<string|\null|int> */',
        ];

        yield [
            '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
            '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
        ];

        yield [
            '<?php /** @var Foo|Foo[]|Foo\Bar|Foo_Bar|null */',
            '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
        ];

        yield [
            '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
        ];

        yield [
            '<?php /** @return A&B&C */',
            '<?php /** @return A&C&B */',
        ];

        yield [
            '<?php /** @return array<A&B&C> */',
            '<?php /** @return array<A&C&B> */',
        ];

        yield [
            '<?php /** @return array<A&B&C>|bool|string */',
            '<?php /** @return bool|array<A&B&C>|string */',
        ];

        yield [
            '<?php /** @return A&B<X|Y|Z>&C&D */',
            '<?php /** @return A&D&B<X|Y|Z>&C */',
        ];
    }

    /**
     * @dataProvider provideFixWithAlphaAlgorithmAndNullAlwaysFirstCases
     */
    public function testFixWithAlphaAlgorithmAndNullAlwaysFirst(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'always_first',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithAlphaAlgorithmAndNullAlwaysFirstCases(): iterable
    {
        yield [
            '<?php /** @var null|int|string */',
            '<?php /** @var string|int|null */',
        ];

        yield [
            '<?php /** @param Bar|\Foo */',
            '<?php /** @param \Foo|Bar */',
        ];

        yield [
            '<?php /** @property-read \Bar|Foo */',
            '<?php /** @property-read Foo|\Bar */',
        ];

        yield [
            '<?php /** @property-write bar|Foo */',
            '<?php /** @property-write Foo|bar */',
        ];

        yield [
            '<?php /** @return Bar|foo */',
            '<?php /** @return foo|Bar */',
        ];

        yield [
            '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
            '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
        ];

        yield [
            '<?php /** @var null|array|Bar\Baz|bool[]|false|Foo|int|object|resource|string|string[] */',
            '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
        ];

        yield [
            '<?php /** @var null|array<int, string> Foo */',
        ];

        yield [
            '<?php /** @var null|array<int, array<string>> Foo */',
        ];

        yield [
            '<?php /** @var NULL|int|string */',
            '<?php /** @var string|int|NULL */',
        ];

        yield [
            '<?php /** @var ?Bar|Foo */',
            '<?php /** @var Foo|?Bar */',
        ];

        yield [
            '<?php /** @var Bar|?Foo */',
            '<?php /** @var ?Foo|Bar */',
        ];

        yield [
            '<?php /** @var ?\Bar|Foo */',
            '<?php /** @var Foo|?\Bar */',
        ];

        yield [
            '<?php /** @var Bar|?\Foo */',
            '<?php /** @var ?\Foo|Bar */',
        ];

        yield [
            '<?php /** @var array<null|int|string> */',
            '<?php /** @var array<string|int|null> */',
        ];

        yield [
            '<?php /** @var array<int|string, null|int|string> */',
            '<?php /** @var array<string|int, string|int|null> */',
        ];

        yield [
            '<?php /** @var array<int|string,     array<int|string, null|int|string>> */',
            '<?php /** @var array<string|int,     array<string|int, string|int|null>> */',
        ];

        yield [
            '<?php /** @var null|null */',
        ];

        yield [
            '<?php /** @var null|\null */',
        ];

        yield [
            '<?php /** @var \null|null */',
        ];

        yield [
            '<?php /** @var \null|\null */',
        ];

        yield [
            '<?php /** @var array<\null|int|string> */',
            '<?php /** @var array<string|\null|int> */',
        ];

        yield [
            '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
            '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
        ];

        yield [
            '<?php /** @var null|Foo|Foo[]|Foo\Bar|Foo_Bar */',
            '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
        ];

        yield [
            '<?php /** @return array<array<string, int>> */',
        ];

        yield [
            '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
        ];
    }

    /**
     * @dataProvider provideFixWithAlphaAlgorithmAndNullAlwaysLastCases
     */
    public function testFixWithAlphaAlgorithmAndNullAlwaysLast(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'always_last',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithAlphaAlgorithmAndNullAlwaysLastCases(): iterable
    {
        yield [
            '<?php /** @var int|string|null */',
            '<?php /** @var string|int|null */',
        ];

        yield [
            '<?php /** @param Bar|\Foo */',
            '<?php /** @param \Foo|Bar */',
        ];

        yield [
            '<?php /** @property-read \Bar|Foo */',
            '<?php /** @property-read Foo|\Bar */',
        ];

        yield [
            '<?php /** @property-write bar|Foo */',
            '<?php /** @property-write Foo|bar */',
        ];

        yield [
            '<?php /** @return Bar|foo */',
            '<?php /** @return foo|Bar */',
        ];

        yield [
            '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
            '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
        ];

        yield [
            '<?php /** @var array|Bar\Baz|bool[]|false|Foo|int|object|resource|string|string[]|null */',
            '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
        ];

        yield [
            '<?php /** @var array<int, string>|null Foo */',
            '<?php /** @var null|array<int, string> Foo */',
        ];

        yield [
            '<?php /** @var array<int, array<string>>|null Foo */',
            '<?php /** @var null|array<int, array<string>> Foo */',
        ];

        yield [
            '<?php /** @var int|string|NULL */',
            '<?php /** @var string|int|NULL */',
        ];

        yield [
            '<?php /** @var ?Bar|Foo */',
            '<?php /** @var Foo|?Bar */',
        ];

        yield [
            '<?php /** @var Bar|?Foo */',
            '<?php /** @var ?Foo|Bar */',
        ];

        yield [
            '<?php /** @var ?\Bar|Foo */',
            '<?php /** @var Foo|?\Bar */',
        ];

        yield [
            '<?php /** @var Bar|?\Foo */',
            '<?php /** @var ?\Foo|Bar */',
        ];

        yield [
            '<?php /** @var array<int|string|null> */',
            '<?php /** @var array<string|int|null> */',
        ];

        yield [
            '<?php /** @var array<int|string, int|string|null> */',
            '<?php /** @var array<string|int, string|int|null> */',
        ];

        yield [
            '<?php /** @var array<int|string,     array<int|string, int|string|null>> */',
            '<?php /** @var array<string|int,     array<string|int, string|int|null>> */',
        ];

        yield [
            '<?php /** @var null|null */',
        ];

        yield [
            '<?php /** @var null|\null */',
        ];

        yield [
            '<?php /** @var \null|null */',
        ];

        yield [
            '<?php /** @var \null|\null */',
        ];

        yield [
            '<?php /** @var array<int|string|\null> */',
            '<?php /** @var array<string|\null|int> */',
        ];

        yield [
            '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
        ];

        yield [
            '<?php /** @var Foo|Foo[]|Foo\Bar|Foo_Bar|null */',
            '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
        ];

        yield [
            '<?php /** @return array<int, callable(array<string, string|null> , DateTime): bool> */',
            '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
        ];

        yield [
            '<?php /** @var ?Deferred<TestLocations> */',
        ];
    }

    /**
     * @dataProvider provideFixWithCaseSensitiveCases
     */
    public function testFixWithCaseSensitive(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'case_sensitive' => true,
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithCaseSensitiveCases(): iterable
    {
        return [
            [
                '<?php /** @var AA|Aaa */',
                '<?php /** @var Aaa|AA */',
            ],
        ];
    }
}
