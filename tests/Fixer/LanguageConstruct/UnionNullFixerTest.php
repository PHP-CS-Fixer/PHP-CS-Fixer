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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\UnionNullFixer
 *
 * @requires PHP 8.0
 */
final class UnionNullFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideReturnTypeCases
     */
    public function testReturnType(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideReturnTypeCases(): iterable
    {
        yield [
            '<?php function foo(): string|null {} ?>',
            '<?php function foo(): ?string {} ?>',
        ];

        yield [
            '<?php $fn = fn (): string|null => null; ?>',
            '<?php $fn = fn (): ?string => null; ?>',
        ];
    }

    /**
     * @dataProvider provideParameterCases
     */
    public function testParameter(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideParameterCases(): iterable
    {
        yield [
            '<?php function foo(int|null $a = null) {} ?>',
            '<?php function foo(?int $a = null) {} ?>',
        ];

        yield [
            '<?php $a = fn (int|null $a = null) => $a; ?>',
            '<?php $a = fn (?int $a = null) => $a; ?>',
        ];

        yield [
            "<?php class Foo {\npublic function foo(int|null \$a = null) {} }\n?>",
            "<?php class Foo {\npublic function foo(?int \$a = null) {} }\n?>",
        ];

        yield [
            "<?php class Foo {\npublic static function foo(int|null \$a = null) {} }\n?>",
            "<?php class Foo {\npublic static function foo(?int \$a = null) {} }\n?>",
        ];

        yield [
            "<?php class Foo {\npublic function foo() {\n\$fn = fn (int|null \$a = null) => \$a;\n} }\n?>",
            "<?php class Foo {\npublic function foo() {\n\$fn = fn (?int \$a = null) => \$a;\n} }\n?>",
        ];

        yield [
            "<?php class Foo {\npublic static function foo() {\n\$fn = fn (int|null \$a = null) => \$a;\n} }\n?>",
            "<?php class Foo {\npublic static function foo() {\n\$fn = fn (?int \$a = null) => \$a;\n} }\n?>",
        ];
    }

    /**
     * @dataProvider providePropertyCases
     */
    public function testProperty(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function providePropertyCases(): iterable
    {
        $template = <<<'CLASS'
<?php

class Foo
{
    private {type} $foo = null;
    private static {type} $fooS = null;
    protected {type} $foo3 = null;
    protected static {type} $fooS2 = null;
    public {type} $foo5 = null;
    public static {type} $fooS3 = null;
}

?>
CLASS;

        $build = fn (string $a, string $b) => [
            str_replace('{type}', $a, $template),
            str_replace('{type}', $b, $template),
        ];

        yield $build(
            'int|null',
            '?int'
        );

        yield $build(
            'string|null',
            '?string'
        );

        yield $build(
            'object|null',
            '?object'
        );
    }

    /**
     * @dataProvider providePropertyReadonlyCases
     */
    public function testPropertyReadonly(string $expected, ?string $input = null): void
    {
        if (\PHP_VERSION_ID < 8_01_00) {
            self::markTestSkipped('PHP >= 8.1 is required.');
        }

        $this->doTest($expected, $input);
    }

    public static function providePropertyReadonlyCases(): iterable
    {
        $template = <<<'CLASS'
<?php

class Foo
{
    private readonly {type} $foo;
    protected readonly {type} $foo2;
    public readonly {type} $foo3;
}

?>
CLASS;

        $build = fn (string $a, string $b) => [
            str_replace('{type}', $a, $template),
            str_replace('{type}', $b, $template),
        ];

        yield $build(
            'int|null',
            '?int'
        );

        yield $build(
            'string|null',
            '?string'
        );

        yield $build(
            'object|null',
            '?object'
        );
    }
}
