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
 */
final class UnionNullFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideParameters
     */
    public function testParameters(string $expected, ?string $input = null): void
    {
        if (\PHP_VERSION_ID < 8_00_00) {
            self::markTestSkipped('PHP >= 8.0 is required.');
        }

        $this->doTest($expected, $input);
    }

    public static function provideParameters(): iterable
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
     * @dataProvider provideProperties
     */
    public function testProperties(string $expected, ?string $input = null): void
    {
        if (\PHP_VERSION_ID < 8_00_00) {
            self::markTestSkipped('PHP >= 8.0 is required.');
        }

        $this->doTest($expected, $input);
    }

    public static function provideProperties(): iterable
    {
        $template = <<<'CLASS'
<?php

class Foo
{
    private {type} $foo = null;
    private readonly {type} $foo2;
    private static {type} $fooS = null;
    protected {type} $foo3 = null;
    protected readonly {type} $foo4;
    protected static {type} $fooS2 = null;
    public {type} $foo5 = null;
    public readonly {type} $foo6;
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
}
