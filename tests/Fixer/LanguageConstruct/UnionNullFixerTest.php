<?php

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

final class UnionNullFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideParameters
     */
    public function testParameters(string $expected, ?string $input = null): void
    {
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
            "<?php class Foo {\npublic function foo(?int \$a = null) {} }\n?>"
        ];

        yield [
            "<?php class Foo {\npublic function foo() {\n\$fn = fn (int|null \$a = null) => \$a;\n} }\n?>",
            "<?php class Foo {\npublic function foo() {\n\$fn = fn (?int \$a = null) => \$a;\n} }\n?>",
        ];
    }

    /**
     * @dataProvider provideProperties
     */
    public function testProperties(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideProperties(): iterable
    {
        $template = <<<CLASS
<?php

class Foo
{
    private {type} \$foo = null;
    private readonly {type} \$foo2;
    protected {type} \$foo3 = null;
    protected readonly {type} \$foo4;
    public {type} \$foo5 = null;
    public readonly {type} \$foo6;
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
