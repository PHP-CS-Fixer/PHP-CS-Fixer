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

namespace Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\FullyQualifiedNameAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\FullyQualifiedNameAnalyzer
 */
final class FullyQualifiedNameAnalyzerTest extends TestCase
{
    /**
     * @dataProvider provideGetFullyQualifiedNameCases
     */
    public function testGetFullyQualifiedName(string $fullyQualifiedName, string $code, string $name, int $index): void
    {
        self::assertSame(
            $fullyQualifiedName,
            FullyQualifiedNameAnalyzer::getFullyQualifiedName(Tokens::fromCode($code), $name, $index)
        );
    }

    /**
     * @return iterable<array{string, string, string, int}>
     */
    public static function provideGetFullyQualifiedNameCases(): iterable
    {
        yield 'no namespace and no import' => [
            'Foo\Bar\Baz',
            '<?php function f(Foo\Bar\Baz $x) {}',
            'Foo\Bar\Baz',
            5,
        ];

        yield 'no namespace and import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            14,
        ];

        yield 'no namespace and import with leading slash' => [
            'Foo\Bar\Baz',
            '<?php use \Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            15,
        ];

        yield 'no namespace and partial import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar; function f(Bar\Baz $x) {}',
            'Bar\Baz',
            12,
        ];

        yield 'no namespace and aliased import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar\Baz as TheClass; function f(TheClass $x) {}',
            'TheClass',
            18,
        ];

        yield 'no namespace and partial aliased import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar as TheClass; function f(TheClass\Baz $x) {}',
            'TheClass\Baz',
            16,
        ];

        yield 'namespaced with no import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; function f(\Foo\Bar\Baz $x) {}',
            '\Foo\Bar\Baz',
            10,
        ];

        yield 'namespaced with import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            19,
        ];

        yield 'namespaced with import with leading slash' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use \Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            20,
        ];

        yield 'namespaced with partial import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar; function f(Bar\Baz $x) {}',
            'Bar\Baz',
            17,
        ];

        yield 'namespaced with aliased import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar\Baz as TheClass; function f(TheClass $x) {}',
            'TheClass',
            23,
        ];

        yield 'namespaced with partial aliased import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar as TheClass; function f(TheClass\Baz $x) {}',
            'TheClass\Baz',
            21,
        ];

        yield 'multiple imports' => [
            'FooClass\Bar',
            <<<'PHP'
                <?php
                namespace N;
                use FooClass\Foo;
                use FooClass\Bar;
                use FooClass\Baz;
                function f(Bar $x) {}
                PHP,
            'Bar',
            20,
        ];

        yield 'imports of all kinds' => [
            'FooClass\Bar',
            <<<'PHP'
                <?php
                namespace N;
                use const FooConst\Bar;
                use FooClass\Bar;
                use function FooFunction\Bar;
                function f(Bar $x) {}
                PHP,
            'Bar',
            35,
        ];
    }
}
