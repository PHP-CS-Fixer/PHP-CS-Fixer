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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FullyQualifiedNameAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\FullyQualifiedNameAnalyzer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class FullyQualifiedNameAnalyzerTest extends TestCase
{
    /**
     * @dataProvider provideGetFullyQualifiedNameCases
     *
     * @param NamespaceUseAnalysis::TYPE_* $importType
     */
    public function testGetFullyQualifiedName(string $fullyQualifiedName, string $code, string $name, int $indexInNamespace, int $importType): void
    {
        $analyzer = new FullyQualifiedNameAnalyzer(Tokens::fromCode($code));
        self::assertSame(
            $fullyQualifiedName,
            $analyzer->getFullyQualifiedName($name, $indexInNamespace, $importType),
        );
    }

    /**
     * @return iterable<string, array{string, string, string, int}>
     */
    public static function provideGetFullyQualifiedNameCases(): iterable
    {
        yield 'no namespace and no import' => [
            'Foo\Bar\Baz',
            '<?php function f(Foo\Bar\Baz $x) {}',
            'Foo\Bar\Baz',
            5,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'no namespace and import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            14,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'no namespace and import with leading slash' => [
            'Foo\Bar\Baz',
            '<?php use \Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            15,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'no namespace and partial import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar; function f(Bar\Baz $x) {}',
            'Bar\Baz',
            12,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'no namespace and aliased import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar\Baz as TheClass; function f(TheClass $x) {}',
            'TheClass',
            18,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'no namespace and partial aliased import' => [
            'Foo\Bar\Baz',
            '<?php use Foo\Bar as TheClass; function f(TheClass\Baz $x) {}',
            'TheClass\Baz',
            16,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'namespaced with no import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; function f(\Foo\Bar\Baz $x) {}',
            '\Foo\Bar\Baz',
            10,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'namespaced with import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            19,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'namespaced with import with leading slash' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use \Foo\Bar\Baz; function f(Baz $x) {}',
            'Baz',
            20,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'namespaced with partial import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar; function f(Bar\Baz $x) {}',
            'Bar\Baz',
            17,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'namespaced with aliased import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar\Baz as TheClass; function f(TheClass $x) {}',
            'TheClass',
            23,
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'namespaced with partial aliased import' => [
            'Foo\Bar\Baz',
            '<?php namespace N; use Foo\Bar as TheClass; function f(TheClass\Baz $x) {}',
            'TheClass\Baz',
            21,
            NamespaceUseAnalysis::TYPE_CLASS,
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
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'imports of all kinds - resolve classy' => [
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
            NamespaceUseAnalysis::TYPE_CLASS,
        ];

        yield 'imports of all kinds - resolve constant' => [
            'FooConst\Bar',
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
            NamespaceUseAnalysis::TYPE_CONSTANT,
        ];

        yield 'imports of all kinds - resolve function' => [
            'FooFunction\Bar',
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
            NamespaceUseAnalysis::TYPE_FUNCTION,
        ];

        $indexToNameMap = [
            11 => ['Foo', 'Namespace1\Foo'],
            31 => ['Foo', 'Namespace2\Foo'],
            51 => ['Foo', 'Namespace3\Foo'],
            55 => ['Bar', 'Namespace3\Bar'],
        ];
        foreach ($indexToNameMap as $index => [$shortName, $fullName]) {
            yield \sprintf('multiple namespaces with class %s', $fullName) => [
                $fullName,
                <<<'PHP'
                    <?php
                    namespace Namespace1 { function f(Foo $x) {} }
                    namespace Namespace2 { function f(Foo $x) {} }
                    namespace Namespace3 { function f(Foo $x, Bar $t) {} }
                    PHP,
                $shortName,
                $index,
                NamespaceUseAnalysis::TYPE_CLASS,
            ];
        }
    }

    public function testMultipleGetFullyQualifiedNameCalls(): void
    {
        $analyzer = new FullyQualifiedNameAnalyzer(Tokens::fromCode(
            <<<'PHP'
                <?php
                namespace Namespace1 { use Vendor1\Foo; function f(Foo $x) {} }
                namespace Namespace2 { use Vendor1\Bar; function f(Bar $x) {} }
                namespace Namespace1 { use Vendor2\Foo; function f(Foo $x) {} }
                PHP,
        ));

        self::assertSame('Vendor1\Foo', $analyzer->getFullyQualifiedName('Foo', 18, NamespaceUseAnalysis::TYPE_CLASS));
        self::assertSame('Vendor1\Bar', $analyzer->getFullyQualifiedName('Bar', 45, NamespaceUseAnalysis::TYPE_CLASS));
        self::assertSame('Vendor2\Foo', $analyzer->getFullyQualifiedName('Foo', 72, NamespaceUseAnalysis::TYPE_CLASS));
    }
}
