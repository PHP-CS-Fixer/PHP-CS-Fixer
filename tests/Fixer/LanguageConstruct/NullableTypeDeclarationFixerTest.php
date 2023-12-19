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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\NullableTypeDeclarationFixer
 */
final class NullableTypeDeclarationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.0
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: ?string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'scalar with null' => [
            "<?php\nfunction foo(?int \$bar): void {}\n",
            "<?php\nfunction foo(int|null \$bar): void {}\n",
        ];

        yield 'class with null' => [
            "<?php\nfunction bar(?\\stdClass \$crate): int {}\n",
            "<?php\nfunction bar(null | \\stdClass \$crate): int {}\n",
        ];

        yield 'static null' => [
            '<?php
class Foo
{
    public function bar(?array $config = null): ?static {}
}
',
            '<?php
class Foo
{
    public function bar(null|array $config = null): null|static {}
}
',
        ];

        yield 'multiple parameters' => [
            "<?php\nfunction baz(?Foo \$foo, int|string \$value, ?array \$config = null): ?int {}\n",
            "<?php\nfunction baz(null|Foo \$foo, int|string \$value, null|array \$config = null): int|null {}\n",
        ];

        yield 'class properties' => [
            '<?php
class Dto
{
    public ?string $name;
    public ?array $parameters;
    public ?int $count;
    public ?Closure $callable;
}
',
            '<?php
class Dto
{
    public null|string $name;
    public array|null $parameters;
    public int|null $count;
    public null|Closure $callable;
}
',
        ];

        yield 'skips more than two atomic types' => [
            "<?php\nstatic fn (int|null|string \$bar): bool => true;\n",
        ];

        yield 'skips already fixed' => [
            "<?php\n\$bar = function (?string \$input): int {};\n",
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['syntax' => 'union']);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: ?string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'scalar with null' => [
            "<?php\nfunction foo(null|int \$bar): void {}\n",
            "<?php\nfunction foo(?int \$bar): void {}\n",
        ];

        yield 'class with null' => [
            "<?php\nfunction bar(null|\\stdClass \$crate): int {}\n",
            "<?php\nfunction bar(?\\stdClass \$crate): int {}\n",
        ];

        yield 'static null' => [
            '<?php
class Foo
{
    public function bar(null|array $config = null): null|static {}
}
',
            '<?php
class Foo
{
    public function bar(?array $config = null): ?static {}
}
',
        ];

        yield 'multiple parameters' => [
            "<?php\nfunction baz(null|Foo \$foo, int|string \$value, null|array \$config = null): null|int {}\n",
            "<?php\nfunction baz(?Foo \$foo, int|string \$value, ?array \$config = null): ?int {}\n",
        ];

        yield 'class properties' => [
            '<?php
class Dto
{
    public null|\Closure $callable;
    public null|string $name;
    public null|array $parameters;
    public null|int $count;
}
',
            '<?php
class Dto
{
    public ?\Closure $callable;
    public ?string $name;
    public ?array $parameters;
    public ?int $count;
}
',
        ];

        yield 'space after ?' => [
            '<?php
class Foo
{
    private null|int $x;

    public static function from(null|int $x): null|static {}
}
',
            '<?php
class Foo
{
    private ? int $x;

    public static function from(?  int $x): ? static {}
}
',
        ];

        yield 'skips already fixed' => [
            "<?php\n\$bar = function (null | string \$input): int {};\n",
        ];
    }

    /**
     * @param null|array<string, string> $config
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'readonly property' => [
            '<?php
class Qux
{
    public readonly ?int $baz;
}
',
            '<?php
class Qux
{
    public readonly int|null $baz;
}
',
        ];

        yield 'readonly property with union syntax expected' => [
            '<?php
class Qux
{
    public readonly null|int $baz;
}
',
            '<?php
class Qux
{
    public readonly ?int $baz;
}
',
            ['syntax' => 'union'],
        ];
    }

    /**
     * @param null|array<string, string> $config
     *
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFix82Cases(): iterable
    {
        yield 'skips DNF types' => [
            '<?php
class Infinite
{
    private static (A&B)|null $dft;
}
',
        ];
    }
}
