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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @requires PHP 7.1
 *
 * @author Jack Cherng <jfcherng@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\CompactNullableTypehintFixer
 */
final class CompactNullableTypehintFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFix71Cases
     */
    public function testFix71(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix71Cases(): array
    {
        return [
            [
                '<?php function foo(?int $param): ?int {}',
            ],
            [
                '<?php function foo(? /* foo */ int $param): ? /* foo */ int {}',
            ],
            [
                '<?php function foo(? /** foo */ int $param): ? /** foo */ int {}',
            ],
            [
                '<?php function foo(? // foo
                    int $param): ? // foo
                    int {}',
            ],
            [
                '<?php function foo(/**? int*/$param): ?int {}',
                '<?php function foo(/**? int*/$param): ? int {}',
            ],
            [
                '<?php function foo(?callable $param): ?callable {}',
                '<?php function foo(? callable $param): ? callable {}',
            ],
            [
                '<?php function foo(?array &$param): ?array {}',
                '<?php function foo(? array &$param): ? array {}',
            ],
            [
                '<?php function foo(?Bar $param): ?Bar {}',
                '<?php function foo(? Bar $param): ? Bar {}',
            ],
            [
                '<?php function foo(?\Bar $param): ?\Bar {}',
                '<?php function foo(? \Bar $param): ? \Bar {}',
            ],
            [
                '<?php function foo(?Bar\Baz $param): ?Bar\Baz {}',
                '<?php function foo(? Bar\Baz $param): ? Bar\Baz {}',
            ],
            [
                '<?php function foo(?Bar\Baz &$param): ?Bar\Baz {}',
                '<?php function foo(? Bar\Baz &$param): ? Bar\Baz {}',
            ],
            [
                '<?php $foo = function(?Bar\Baz $param): ?Bar\Baz {};',
                '<?php $foo = function(? Bar\Baz $param): ? Bar\Baz {};',
            ],
            [
                '<?php $foo = function(?Bar\Baz &$param): ?Bar\Baz {};',
                '<?php $foo = function(? Bar\Baz &$param): ? Bar\Baz {};',
            ],
            [
                '<?php class Test { public function foo(?Bar\Baz $param): ?Bar\Baz {} }',
                '<?php class Test { public function foo(? Bar\Baz $param): ? Bar\Baz {} }',
            ],
            [
                '<?php abstract class Test { abstract public function foo(?Bar\Baz $param); }',
                '<?php abstract class Test { abstract public function foo(? Bar\Baz $param); }',
            ],
            [
                '<?php $foo = function(?array $a,
                    ?array $b): ?Bar\Baz {};',
                '<?php $foo = function(?
                    array $a,
                    ? array $b): ?
                    Bar\Baz {};',
            ],
            [
                '<?php function foo(?array ...$param): ?array {}',
                '<?php function foo(? array ...$param): ? array {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFix74Cases
     * @requires PHP 7.4
     */
    public function testFix74(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix74Cases(): array
    {
        return [
            [
                '<?php class Foo { private ?string $foo; }',
                '<?php class Foo { private ? string $foo; }',
            ],
            [
                '<?php class Foo { protected ?string $foo; }',
                '<?php class Foo { protected ? string $foo; }',
            ],
            [
                '<?php class Foo { public ?string $foo; }',
                '<?php class Foo { public ? string $foo; }',
            ],
            [
                '<?php class Foo { var ?Foo\Bar $foo; }',
                '<?php class Foo { var ? Foo\Bar $foo; }',
            ],
            [
                '<?php $foo = fn(?Bar\Baz $param): ?Bar\Baz => null;',
                '<?php $foo = fn(? Bar\Baz $param): ? Bar\Baz => null;',
            ],
            [
                '<?php $foo = fn(?Bar\Baz &$param): ?Bar\Baz => null;',
                '<?php $foo = fn(? Bar\Baz &$param): ? Bar\Baz => null;',
            ],
            [
                '<?php $foo = fn(?array $a,
                    ?array $b): ?Bar\Baz => null;',
                '<?php $foo = fn(?
                    array $a,
                    ? array $b): ?
                    Bar\Baz => null;',
            ],
        ];
    }
}
