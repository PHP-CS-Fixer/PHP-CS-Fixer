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
     * @dataProvider provideFixCases
     */
    public function testFixWithNullFirst(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'sort_algorithm' => 'none',
            'null_adjustment' => 'always_first',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            [
                '<?php /** @var null|string */',
                '<?php /** @var string|null */',
            ],
            [
                '<?php /** @param null|string $foo */',
                '<?php /** @param string|null $foo */',
            ],
            [
                '<?php /** @property null|string $foo */',
                '<?php /** @property string|null $foo */',
            ],
            [
                '<?php /** @property-read null|string $foo */',
                '<?php /** @property-read string|null $foo */',
            ],
            [
                '<?php /** @property-write null|string $foo */',
                '<?php /** @property-write string|null $foo */',
            ],
            [
                '<?php /** @method null|string foo(null|int $foo, null|string $bar) */',
                '<?php /** @method string|null foo(int|null $foo, string|null $bar) */',
            ],
            [
                '<?php /** @return null|string */',
                '<?php /** @return string|null */',
            ],
            [
                '<?php /** @var null|string[]|resource|false|object|Foo|Bar\Baz|bool[]|string|array|int */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var null|array<int, string> Foo */',
                '<?php /** @var array<int, string>|null Foo */',
            ],
            [
                '<?php /** @var null|array<int, array<string>> Foo */',
                '<?php /** @var array<int, array<string>>|null Foo */',
            ],
            [
                '<?php /** @var NULL|string */',
                '<?php /** @var string|NULL */',
            ],
            [
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var array<null|string> */',
                '<?php /** @var array<string|null> */',
            ],
            [
                '<?php /** @var array<int, null|string> */',
                '<?php /** @var array<int, string|null> */',
            ],
            [
                '<?php /** @var array<int,     array<null|int|string>> */',
                '<?php /** @var array<int,     array<int|string|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var \null|int */',
                '<?php /** @var int|\null */',
            ],
            [
                '<?php /** @var array<\null|int> */',
                '<?php /** @var array<int|\null> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            ],
            [
                '<?php /** @var null|Foo[]|Foo|Foo\Bar|Foo_Bar */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
            [
                '<?php /** @method void bar(null|string $delimiter = \',<br/>\') */',
                '<?php /** @method void bar(string|null $delimiter = \',<br/>\') */',
            ],
            [
                '<?php /** @var array<array<int, int>, OutputInterface> */',
            ],
            [
                '<?php /** @var iterable<array{names:array<string>, surname:string}> */',
            ],
            [
                '<?php /** @var iterable<array{surname:string, names:array<string>}> */',
            ],
            [
                '<?php /** @return array<array{level:string, message:string, context:array<mixed>}> */',
            ],
            [
                '<?php /** @return Data<array{enabled: string[], all: array<string, string>}> */',
            ],
            [
                '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
            ],
            [
                '<?php /** @param null|callable(array<string>): array<string, T> $callback */',
            ],
            [
                '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
            ],
            [
                '<?php /** @return Closure(Iterator<TKey, T>): Generator<int, array<TKey, T>> */',
            ],
            [
                '<?php /** @var Closure(Iterator<TKey, T>): Generator<int, array<TKey, T>> $pipe */',
            ],
            [
                '<?php /** @return Generator<int, Promise<mixed>, mixed, Identity> */',
            ],
            [
                '<?php /** @param null|callable(null|foo, null|bar): array<string, T> $callback */',
                '<?php /** @param null|callable(foo|null, bar|null): array<string, T> $callback */',
            ],
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

    public static function provideFixWithNullLastCases(): array
    {
        return [
            [
                '<?php /** @var string|null */',
                '<?php /** @var null|string */',
            ],
            [
                '<?php /** @param string|null $foo */',
                '<?php /** @param null|string $foo */',
            ],
            [
                '<?php /** @property string|null $foo */',
                '<?php /** @property null|string $foo */',
            ],
            [
                '<?php /** @property-read string|null $foo */',
                '<?php /** @property-read null|string $foo */',
            ],
            [
                '<?php /** @property-write string|null $foo */',
                '<?php /** @property-write null|string $foo */',
            ],
            [
                '<?php /** @method string|null foo(int|null $foo, string|null $bar) */',
                '<?php /** @method null|string foo(null|int $foo, null|string $bar) */',
            ],
            [
                '<?php /** @return string|null */',
                '<?php /** @return null|string */',
            ],
            [
                '<?php /** @var string[]|resource|false|object|Foo|Bar\Baz|bool[]|string|array|int|null */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var array<int, string>|null Foo */',
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var array<int, array<string>>|null Foo */',
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var string|NULL */',
                '<?php /** @var NULL|string */',
            ],
            [
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<string|null> */',
                '<?php /** @var array<null|string> */',
            ],
            [
                '<?php /** @var array<int, string|null> */',
                '<?php /** @var array<int, null|string> */',
            ],
            [
                '<?php /** @var array<int,     array<int|string|null>> */',
                '<?php /** @var array<int,     array<null|int|string>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var int|\null */',
                '<?php /** @var \null|int */',
            ],
            [
                '<?php /** @var array<int|\null> */',
                '<?php /** @var array<\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
            ],
            [
                '<?php /** @var Foo[]|Foo|Foo\Bar|Foo_Bar|null */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
            [
                '<?php /** @return array<int, callable(array<string, string|null> , DateTime): bool> */',
                '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
            ],
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

    public static function provideFixWithAlphaAlgorithmCases(): array
    {
        return [
            [
                '<?php /** @var int|null|string */',
                '<?php /** @var string|int|null */',
            ],
            [
                '<?php /** @param Bar|\Foo */',
                '<?php /** @param \Foo|Bar */',
            ],
            [
                '<?php /** @property-read \Bar|Foo */',
                '<?php /** @property-read Foo|\Bar */',
            ],
            [
                '<?php /** @property-write bar|Foo */',
                '<?php /** @property-write Foo|bar */',
            ],
            [
                '<?php /** @return Bar|foo */',
                '<?php /** @return foo|Bar */',
            ],
            [
                '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
                '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
            ],
            [
                '<?php /** @var array|Bar\Baz|bool[]|false|Foo|int|null|object|resource|string|string[] */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var array<int, string>|null Foo */',
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var array<int, array<string>>|null Foo */',
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var ?Bar|Foo */',
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var Bar|?Foo */',
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var ?\Bar|Foo */',
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var Bar|?\Foo */',
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<null|string> */',
                '<?php /** @var array<string|null> */',
            ],
            [
                '<?php /** @var array<int|string, null|string> */',
                '<?php /** @var array<string|int, string|null> */',
            ],
            [
                '<?php /** @var array<int|string,     array<int|string, null|string>> */',
                '<?php /** @var array<string|int,     array<string|int, string|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var int|\null|string */',
                '<?php /** @var string|\null|int */',
            ],
            [
                '<?php /** @var array<int|\null|string> */',
                '<?php /** @var array<string|\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            ],
            [
                '<?php /** @var Foo|Foo[]|Foo\Bar|Foo_Bar|null */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
            [
                '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
            ],
            [
                '<?php /** @return A&B&C */',
                '<?php /** @return A&C&B */',
            ],
            [
                '<?php /** @return array<A&B&C> */',
                '<?php /** @return array<A&C&B> */',
            ],
            [
                '<?php /** @return array<A&B&C>|bool|string */',
                '<?php /** @return bool|array<A&B&C>|string */',
            ],
            [
                '<?php /** @return A&B<X|Y|Z>&C&D */',
                '<?php /** @return A&D&B<X|Y|Z>&C */',
            ],
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

    public static function provideFixWithAlphaAlgorithmAndNullAlwaysFirstCases(): array
    {
        return [
            [
                '<?php /** @var null|int|string */',
                '<?php /** @var string|int|null */',
            ],
            [
                '<?php /** @param Bar|\Foo */',
                '<?php /** @param \Foo|Bar */',
            ],
            [
                '<?php /** @property-read \Bar|Foo */',
                '<?php /** @property-read Foo|\Bar */',
            ],
            [
                '<?php /** @property-write bar|Foo */',
                '<?php /** @property-write Foo|bar */',
            ],
            [
                '<?php /** @return Bar|foo */',
                '<?php /** @return foo|Bar */',
            ],
            [
                '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
                '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
            ],
            [
                '<?php /** @var null|array|Bar\Baz|bool[]|false|Foo|int|object|resource|string|string[] */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var NULL|int|string */',
                '<?php /** @var string|int|NULL */',
            ],
            [
                '<?php /** @var ?Bar|Foo */',
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var Bar|?Foo */',
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var ?\Bar|Foo */',
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var Bar|?\Foo */',
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<null|int|string> */',
                '<?php /** @var array<string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string, null|int|string> */',
                '<?php /** @var array<string|int, string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string,     array<int|string, null|int|string>> */',
                '<?php /** @var array<string|int,     array<string|int, string|int|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var array<\null|int|string> */',
                '<?php /** @var array<string|\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            ],
            [
                '<?php /** @var null|Foo|Foo[]|Foo\Bar|Foo_Bar */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
            [
                '<?php /** @return array<array<string, int>> */',
            ],
            [
                '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
            ],
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

    public static function provideFixWithAlphaAlgorithmAndNullAlwaysLastCases(): array
    {
        return [
            [
                '<?php /** @var int|string|null */',
                '<?php /** @var string|int|null */',
            ],
            [
                '<?php /** @param Bar|\Foo */',
                '<?php /** @param \Foo|Bar */',
            ],
            [
                '<?php /** @property-read \Bar|Foo */',
                '<?php /** @property-read Foo|\Bar */',
            ],
            [
                '<?php /** @property-write bar|Foo */',
                '<?php /** @property-write Foo|bar */',
            ],
            [
                '<?php /** @return Bar|foo */',
                '<?php /** @return foo|Bar */',
            ],
            [
                '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
                '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
            ],
            [
                '<?php /** @var array|Bar\Baz|bool[]|false|Foo|int|object|resource|string|string[]|null */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var array<int, string>|null Foo */',
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var array<int, array<string>>|null Foo */',
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var int|string|NULL */',
                '<?php /** @var string|int|NULL */',
            ],
            [
                '<?php /** @var ?Bar|Foo */',
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var Bar|?Foo */',
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var ?\Bar|Foo */',
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var Bar|?\Foo */',
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<int|string|null> */',
                '<?php /** @var array<string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string, int|string|null> */',
                '<?php /** @var array<string|int, string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string,     array<int|string, int|string|null>> */',
                '<?php /** @var array<string|int,     array<string|int, string|int|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var array<int|string|\null> */',
                '<?php /** @var array<string|\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
            ],
            [
                '<?php /** @var Foo|Foo[]|Foo\Bar|Foo_Bar|null */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
            [
                '<?php /** @return array<int, callable(array<string, string|null> , DateTime): bool> */',
                '<?php /** @return array<int, callable(array<string, null|string> , DateTime): bool> */',
            ],
            [
                '<?php /** @var ?Deferred<TestLocations> */',
            ],
        ];
    }
}
