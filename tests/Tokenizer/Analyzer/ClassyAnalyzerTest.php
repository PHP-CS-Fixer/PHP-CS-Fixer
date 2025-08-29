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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ClassyAnalyzerTest extends TestCase
{
    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsClassyInvocationCases
     */
    public function testIsClassyInvocation(string $source, array $expected): void
    {
        self::assertClassyInvocation($source, $expected);
    }

    /**
     * @return iterable<array{string, array<int, bool>}>
     */
    public static function provideIsClassyInvocationCases(): iterable
    {
        yield [
            '<?php new Foo;',
            [3 => true],
        ];

        yield [
            '<?php new \Foo;',
            [4 => true],
        ];

        yield [
            '<?php new Bar\Foo;',
            [3 => false, 5 => true],
        ];

        yield [
            '<?php new namespace\Foo;',
            [5 => true],
        ];

        yield [
            '<?php Foo::bar();',
            [1 => true, 3 => false],
        ];

        yield [
            '<?php \Foo::bar();',
            [2 => true, 4 => false],
        ];

        yield [
            '<?php Bar\Foo::bar();',
            [1 => false, 3 => true, 5 => false],
        ];

        yield [
            '<?php $foo instanceof Foo;',
            [5 => true],
        ];

        yield [
            '<?php class Foo extends \A {}',
            [3 => false, 8 => true],
        ];

        yield [
            '<?php class Foo implements A, B\C, \D, E {}',
            [3 => false, 7 => true, 10 => false, 12 => true, 16 => true, 19 => true],
        ];

        yield [
            '<?php class Foo { use A, B\C, \D, E { A::bar insteadof \E; } }',
            [3 => false, 9 => true, 12 => false, 14 => true, 18 => true, 21 => true, 25 => true, 32 => true],
        ];

        yield 'with reference' => [
            '<?php function foo(Foo $foo, Bar &$bar, \Baz ...$baz, Foo\Bar $fooBar) {}',
            [3 => false, 5 => true, 10 => true, 17 => true, 23 => false, 25 => true],
        ];

        yield [
            '<?php class Foo { function bar() { parent::bar(); self::baz(); $a instanceof self; } }',
            [3 => false, 9 => false, 15 => false, 17 => false, 22 => false, 24 => false, 33 => false],
        ];

        yield [
            '<?php echo FOO, \BAR;',
            [3 => false, 7 => false],
        ];

        yield [
            '<?php FOO & $bar;',
            [1 => false],
        ];

        yield [
            '<?php foo(); \bar();',
            [1 => false, 7 => false],
        ];

        yield [
            '<?php function foo(): \Foo {}',
            [3 => false, 9 => true],
        ];

        yield [
            '<?php function foo(?Foo $foo, ?Foo\Bar $fooBar): ?\Foo {}',
            [3 => false, 6 => true, 12 => false, 14 => true, 22 => true],
        ];

        yield [
            '<?php function foo(iterable $foo): string {}',
            [3 => false, 5 => false, 11 => false],
        ];

        yield [
            '<?php function foo(?int $foo): ?string {}',
            [3 => false, 6 => false, 13 => false],
        ];

        yield [
            '<?php function foo(int $foo, string &$bar): self {}',
            [3 => false, 5 => false, 10 => false, 17 => false],
        ];

        yield [
            '<?php function foo(): Foo {}',
            [3 => false, 8 => true],
        ];

        foreach (['bool', 'float', 'int', 'parent', 'self', 'string', 'void'] as $returnType) {
            yield [
                \sprintf('<?php function foo(): %s {}', $returnType),
                [3 => false, 8 => false],
            ];
        }

        yield [
            '<?php try {} catch (Foo $e) {}',
            [9 => true],
        ];

        yield [
            '<?php try {} catch (\Foo $e) {}',
            [10 => true],
        ];

        yield [
            '<?php try {} catch (/* ... */ Foo $e /* ... */) {}',
            [11 => true],
        ];

        yield [
            '<?php try {} catch (/* ... */ \Foo $e /* ... */) {}',
            [12 => true],
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsClassyInvocation80Cases
     *
     * @requires PHP 8.0
     */
    public function testIsClassyInvocation80(string $source, array $expected): void
    {
        self::assertClassyInvocation($source, $expected);
    }

    /**
     * @return iterable<int, array{string, array<int, bool>}>
     */
    public static function provideIsClassyInvocation80Cases(): iterable
    {
        yield [
            '<?php function foo(): \Foo|int {}',
            [3 => false, 9 => true, 11 => false],
        ];

        yield [
            '<?php function foo(): \Foo|A|int {}',
            [3 => false, 9 => true, 11 => true, 13 => false],
        ];

        yield [
            '<?php function foo(): int|A|NULL {}',
            [3 => false, 8 => false, 10 => true, 12 => false],
        ];

        yield [
            '<?php function foo(): int|A|false {}',
            [3 => false, 8 => false, 10 => true, 12 => false],
        ];

        yield [
            '<?php #[AnAttribute] class Foo {}',
            [2 => true],
        ];

        yield [
            '<?php try {} catch (Foo) {}',
            [9 => true],
        ];

        yield [
            '<?php try {} catch (\Foo) {}',
            [10 => true],
        ];

        yield [
            '<?php try {} catch (/* non-capturing catch */ Foo /* just because! */) {}',
            [11 => true],
        ];

        yield [
            '<?php try {} catch (/* non-capturing catch */ \Foo /* just because! */) {}',
            [12 => true],
        ];
    }

    /**
     * @param array<int, bool> $expected
     *
     * @dataProvider provideIsClassyInvocation81Cases
     *
     * @requires PHP 8.1
     */
    public function testIsClassyInvocation81(string $source, array $expected): void
    {
        self::assertClassyInvocation($source, $expected);
    }

    /**
     * @return iterable<string, array{string, array<int, bool>}>
     */
    public static function provideIsClassyInvocation81Cases(): iterable
    {
        yield 'never' => [
            '<?php function foo(): never {}',
            [3 => false, 8 => false],
        ];

        yield 'intersection' => [
            '<?php function foo(): \Foo&Bar {}',
            [3 => false, 9 => true, 11 => true],
        ];
    }

    /**
     * @param array<int, bool> $expected
     */
    private static function assertClassyInvocation(string $source, array $expected): void
    {
        $tokens = Tokens::fromCode($source);
        $analyzer = new ClassyAnalyzer();

        foreach ($expected as $index => $isClassy) {
            self::assertSame($isClassy, $analyzer->isClassyInvocation($tokens, $index), \sprintf('Token at index %d should match the expected value "%s".', $index, true === $isClassy ? 'true' : 'false'));
        }
    }
}
