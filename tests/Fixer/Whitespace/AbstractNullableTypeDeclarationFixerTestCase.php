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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @template TFixer of AbstractFixer
 *
 * @internal
 *
 * @extends AbstractFixerTestCase<TFixer>
 *
 * @author Jack Cherng <jfcherng@gmail.com>
 */
abstract class AbstractNullableTypeDeclarationFixerTestCase extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $a instanceof static ? \DateTime::class : $c;',
        ];

        yield [
            '<?php function foo(?int $param): ?int {}',
        ];

        yield [
            '<?php function foo(? /* foo */ int $param): ? /* foo */ int {}',
        ];

        yield [
            '<?php function foo(? /** foo */ int $param): ? /** foo */ int {}',
        ];

        yield [
            '<?php function foo(? // foo
                    int $param): ? // foo
                    int {}',
        ];

        yield [
            '<?php function foo(/**? int*/$param): ?int {}',
            '<?php function foo(/**? int*/$param): ? int {}',
        ];

        yield [
            '<?php function foo(?callable $param): ?callable {}',
            '<?php function foo(? callable $param): ? callable {}',
        ];

        yield [
            '<?php function foo(?array &$param): ?array {}',
            '<?php function foo(? array &$param): ? array {}',
        ];

        yield [
            '<?php function foo(?Bar $param): ?Bar {}',
            '<?php function foo(? Bar $param): ? Bar {}',
        ];

        yield [
            '<?php function foo(?\Bar $param): ?\Bar {}',
            '<?php function foo(? \Bar $param): ? \Bar {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz $param): ?Bar\Baz {}',
            '<?php function foo(? Bar\Baz $param): ? Bar\Baz {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz &$param): ?Bar\Baz {}',
            '<?php function foo(? Bar\Baz &$param): ? Bar\Baz {}',
        ];

        yield [
            '<?php $foo = function(?Bar\Baz $param): ?Bar\Baz {};',
            '<?php $foo = function(? Bar\Baz $param): ? Bar\Baz {};',
        ];

        yield [
            '<?php $foo = function(?Bar\Baz &$param): ?Bar\Baz {};',
            '<?php $foo = function(? Bar\Baz &$param): ? Bar\Baz {};',
        ];

        yield [
            '<?php class Test { public function foo(?Bar\Baz $param): ?Bar\Baz {} }',
            '<?php class Test { public function foo(? Bar\Baz $param): ? Bar\Baz {} }',
        ];

        yield [
            '<?php abstract class Test { abstract public function foo(?Bar\Baz $param); }',
            '<?php abstract class Test { abstract public function foo(? Bar\Baz $param); }',
        ];

        yield [
            '<?php $foo = function(?array $a,
                    ?array $b): ?Bar\Baz {};',
            '<?php $foo = function(?
                    array $a,
                    ? array $b): ?
                    Bar\Baz {};',
        ];

        yield [
            '<?php function foo(?array ...$param): ?array {}',
            '<?php function foo(? array ...$param): ? array {}',
        ];

        yield [
            '<?php class Foo { private ?string $foo; }',
            '<?php class Foo { private ? string $foo; }',
        ];

        yield [
            '<?php class Foo { protected ?string $foo; }',
            '<?php class Foo { protected ? string $foo; }',
        ];

        yield [
            '<?php class Foo { public ?string $foo; }',
            '<?php class Foo { public ? string $foo; }',
        ];

        yield [
            '<?php class Foo { var ?Foo\Bar $foo; }',
            '<?php class Foo { var ? Foo\Bar $foo; }',
        ];

        yield [
            '<?php $foo = fn(?Bar\Baz $param): ?Bar\Baz => null;',
            '<?php $foo = fn(? Bar\Baz $param): ? Bar\Baz => null;',
        ];

        yield [
            '<?php $foo = fn(?Bar\Baz &$param): ?Bar\Baz => null;',
            '<?php $foo = fn(? Bar\Baz &$param): ? Bar\Baz => null;',
        ];

        yield [
            '<?php $foo = fn(?array $a,
                    ?array $b): ?Bar\Baz => null;',
            '<?php $foo = fn(?
                    array $a,
                    ? array $b): ?
                    Bar\Baz => null;',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'static return' => [
            "<?php\nclass Foo { public function bar(): ?static {} }\n",
            "<?php\nclass Foo { public function bar(): ?   static {} }\n",
        ];
    }
}
