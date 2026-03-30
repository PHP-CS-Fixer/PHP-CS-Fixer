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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\ModernizeArrayKeyFunctionsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Alias\ModernizeArrayKeyFunctionsFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ModernizeArrayKeyFunctionsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.5
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: ?string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'normal array_key_first, expression $foo' => [
            '<?php echo array_first($foo);',
            '<?php echo $foo[array_key_first($foo)];',
        ];

        yield 'normal array_key_last, expression $foo' => [
            '<?php echo array_last($foo);',
            '<?php echo $foo[array_key_last($foo)];',
        ];

        // only test for array_key_first, logic is the same for array_key_last

        // simple variable cases
        yield 'normal array_key_first, expression $foo twice' => [
            '<?php $foo = array_first($foo); $bar = array_first($foo) ?>',
            '<?php $foo = $foo[array_key_first($foo)]; $bar = $foo[array_key_first($foo)] ?>',
        ];

        yield 'array_key_first on right-hand side of assignment, expression $foo' => [
            '<?php $bar = array_first($foo);',
            '<?php $bar = $foo[array_key_first($foo)];',
        ];

        yield 'array_key_first on left-hand side of assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] = 0;',
            null,
        ];

        yield 'array_key_first on left-hand side of and-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] &= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of coalesce-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] ??= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of concat-equal assignment, expression $foo' => [
            "<?php \$foo[array_key_first(\$foo)] .= 'd';",
            null,
        ];

        yield 'array_key_first on left-hand side of div-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] /= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of minus-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] -= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of mod-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] %= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of multiply-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] *= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of or-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] |= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of left-shift-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] <<= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of right-shift-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] >>= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of xor-equal assignment, expression $foo' => [
            '<?php $foo[array_key_first($foo)] ^= false;',
            null,
        ];

        // array offset cases
        yield "normal array_key_first, expression \$foo['bar']['baz']" => [
            "<?php echo array_first(\$foo['bar']['baz']);",
            "<?php echo \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])];",
        ];

        yield "array_key_first on right-hand side of assignment, expression \$foo['bar']['baz']" => [
            "<?php \$bar = array_first(\$foo['bar']['baz']);",
            "<?php \$bar = \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])];",
        ];

        yield "array_key_first on left-hand side of assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] = 0;",
            null,
        ];

        yield "array_key_first on left-hand side of and-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] &= false;",
            null,
        ];

        yield "array_key_first on left-hand side of coalesce-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] ??= false;",
            null,
        ];

        yield "array_key_first on left-hand side of concat-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] .= 'd';",
            null,
        ];

        yield "array_key_first on left-hand side of div-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] /= 2;",
            null,
        ];

        yield "array_key_first on left-hand side of minus-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] -= 2;",
            null,
        ];

        yield "array_key_first on left-hand side of mod-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] %= 2;",
            null,
        ];

        yield "array_key_first on left-hand side of multiply-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] *= 2;",
            null,
        ];

        yield "array_key_first on left-hand side of or-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] |= false;",
            null,
        ];

        yield "array_key_first on left-hand side of left-shift-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] <<= 2;",
            null,
        ];

        yield "array_key_first on left-hand side of right-shift-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo['bar']['baz'])] >>= 2;",
            null,
        ];

        yield "array_key_first on left-hand side of xor-equal assignment, expression \$foo['bar']['baz']" => [
            "<?php \$foo['bar']['baz'][array_key_first(\$foo)] ^= false;",
            null,
        ];

        // variable array offset cases
        yield 'normal array_key_first, expression $foo[$bar]' => [
            '<?php echo array_first($foo[$bar]);',
            '<?php echo $foo[$bar][array_key_first($foo[$bar])];',
        ];

        yield 'array_key_first on right-hand side of assignment, expression $foo[$bar]' => [
            '<?php $bar = array_first($foo[$bar]);',
            '<?php $bar = $foo[$bar][array_key_first($foo[$bar])];',
        ];

        yield 'array_key_first on left-hand side of assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] = 0;',
            null,
        ];

        yield 'array_key_first on left-hand side of and-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] &= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of coalesce-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] ??= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of concat-equal assignment, expression $foo[$bar]' => [
            "<?php \$foo[\$bar][array_key_first(\$foo[\$bar])] .= 'd';",
            null,
        ];

        yield 'array_key_first on left-hand side of div-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] /= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of minus-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] -= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of mod-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] %= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of multiply-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] *= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of or-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] |= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of left-shift-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] <<= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of right-shift-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo[$bar])] >>= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of xor-equal assignment, expression $foo[$bar]' => [
            '<?php $foo[$bar][array_key_first($foo)] ^= false;',
            null,
        ];

        // variable object property cases
        yield 'normal array_key_first, expression $foo->$bar' => [
            '<?php echo array_first($foo->$bar);',
            '<?php echo $foo->$bar[array_key_first($foo->$bar)];',
        ];

        yield 'array_key_first on right-hand side of assignment, expression $foo->$bar' => [
            '<?php $bar = array_first($foo->$bar);',
            '<?php $bar = $foo->$bar[array_key_first($foo->$bar)];',
        ];

        yield 'array_key_first on left-hand side of assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] = 0;',
            null,
        ];

        yield 'array_key_first on left-hand side of and-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] &= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of coalesce-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] ??= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of concat-equal assignment, expression $foo->$bar' => [
            "<?php \$foo->\$bar[array_key_first(\$foo->\$bar)] .= 'd';",
            null,
        ];

        yield 'array_key_first on left-hand side of div-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] /= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of minus-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] -= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of mod-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] %= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of multiply-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] *= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of or-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] |= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of left-shift-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] <<= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of right-shift-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo->$bar)] >>= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of xor-equal assignment, expression $foo->$bar' => [
            '<?php $foo->$bar[array_key_first($foo)] ^= false;',
            null,
        ];

        // variable object subproperty cases
        yield 'normal array_key_first, expression $foo->bar->$bar' => [
            '<?php echo array_first($foo->bar->$bar);',
            '<?php echo $foo->bar->$bar[array_key_first($foo->bar->$bar)];',
        ];

        yield 'array_key_first on right-hand side of assignment, expression $foo->bar->$bar' => [
            '<?php $bar = array_first($foo->bar->$bar);',
            '<?php $bar = $foo->bar->$bar[array_key_first($foo->bar->$bar)];',
        ];

        yield 'array_key_first on left-hand side of assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] = 0;',
            null,
        ];

        yield 'array_key_first on left-hand side of and-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] &= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of coalesce-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] ??= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of concat-equal assignment, expression $foo->bar->$bar' => [
            "<?php \$foo->bar->\$bar[array_key_first(\$foo->bar->\$bar)] .= 'd';",
            null,
        ];

        yield 'array_key_first on left-hand side of div-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] /= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of minus-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] -= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of mod-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] %= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of multiply-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] *= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of or-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] |= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of left-shift-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] <<= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of right-shift-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo->bar->$bar)] >>= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of xor-equal assignment, expression $foo->bar->$bar' => [
            '<?php $foo->bar->$bar[array_key_first($foo)] ^= false;',
            null,
        ];

        // nested object property cases
        yield 'normal array_key_first, expression $foo->bar->baz' => [
            '<?php echo array_first($foo->bar->baz);',
            '<?php echo $foo->bar->baz[array_key_first($foo->bar->baz)];',
        ];

        yield 'array_key_first on right-hand side of assignment, expression $foo->bar->baz' => [
            '<?php $bar = array_first($foo->bar->baz);',
            '<?php $bar = $foo->bar->baz[array_key_first($foo->bar->baz)];',
        ];

        yield 'array_key_first on left-hand side of assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] = 0;',
            null,
        ];

        yield 'array_key_first on left-hand side of and-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] &= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of coalesce-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] ??= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of concat-equal assignment, expression $foo->bar->baz' => [
            "<?php \$foo->bar->baz[array_key_first(\$foo->bar->baz)] .= 'd';",
            null,
        ];

        yield 'array_key_first on left-hand side of div-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] /= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of minus-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] -= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of mod-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] %= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of multiply-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] *= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of or-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] |= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of left-shift-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] <<= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of right-shift-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo->bar->baz)] >>= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of xor-equal assignment, expression $foo->bar->baz' => [
            '<?php $foo->bar->baz[array_key_first($foo)] ^= false;',
            null,
        ];

        // constant cases
        yield 'normal array_key_first, expression MY_CONST' => [
            '<?php echo array_first(MY_CONST);',
            '<?php echo MY_CONST[array_key_first(MY_CONST)];',
        ];

        yield 'array_key_first on right-hand side of assignment, expression MY_CONST' => [
            '<?php $bar = array_first(MY_CONST);',
            '<?php $bar = MY_CONST[array_key_first(MY_CONST)];',
        ];

        // class constant cases
        yield 'normal array_key_first, expression Foo::MY_CONST' => [
            '<?php echo array_first(Foo::MY_CONST);',
            '<?php echo Foo::MY_CONST[array_key_first(Foo::MY_CONST)];',
        ];

        yield 'array_key_first on right-hand side of assignment, expression Foo::MY_CONST' => [
            '<?php $bar = array_first(Foo::MY_CONST);',
            '<?php $bar = Foo::MY_CONST[array_key_first(Foo::MY_CONST)];',
        ];

        // class static property cases
        yield 'normal array_key_first, expression Foo::$bar' => [
            '<?php echo array_first(Foo::$bar);',
            '<?php echo Foo::$bar[array_key_first(Foo::$bar)];',
        ];

        yield 'array_key_first on right-hand side of assignment, expression Foo::$bar' => [
            '<?php $bar = array_first(Foo::$bar);',
            '<?php $bar = Foo::$bar[array_key_first(Foo::$bar)];',
        ];

        yield 'array_key_first on left-hand side of assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] = 0;',
            null,
        ];

        yield 'array_key_first on left-hand side of and-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] &= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of coalesce-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] ??= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of concat-equal assignment, expression Foo::$bar' => [
            "<?php Foo::\$bar[array_key_first(Foo::\$bar)] .= 'd';",
            null,
        ];

        yield 'array_key_first on left-hand side of div-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] /= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of minus-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] -= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of mod-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] %= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of multiply-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] *= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of or-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] |= false;',
            null,
        ];

        yield 'array_key_first on left-hand side of left-shift-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] <<= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of right-shift-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first(Foo::$bar)] >>= 2;',
            null,
        ];

        yield 'array_key_first on left-hand side of xor-equal assignment, expression Foo::$bar' => [
            '<?php Foo::$bar[array_key_first($foo)] ^= false;',
            null,
        ];

        // increment/decrement operator
        yield 'array_key_first with increment operator on the right' => [
            '<?php $foo[array_key_first($foo)]++;',
            null,
        ];

        yield 'array_key_first with decrement operator on the right' => [
            '<?php $foo[array_key_first($foo)]--;',
            null,
        ];

        yield 'array_key_first with increment operator on the left' => [
            '<?php --$foo[array_key_first($foo)];',
            null,
        ];

        yield 'array_key_first with decrement operator on the left' => [
            '<?php ++$foo[array_key_first($foo)];',
            null,
        ];

        // non-matching expressions
        yield 'array_key_first when using a function as an expression' => [
            '<?php echo range(1,5)[array_key_first(range(1,5))];',
            null,
        ];

        yield 'array_key_first when using a method as an expression' => [
            '<?php echo $foo->bar()[array_key_first($foo->bar())];',
            null,
        ];

        yield 'array_key_first when using a static function as an expression' => [
            '<?php echo Foo::bar()[array_key_first(Foo::bar())];',
            null,
        ];

        yield 'array_key_first when using different variables' => [
            '<?php echo $foo[array_key_first($bar)];',
            null,
        ];

        yield 'array_key_first when using different attributes' => [
            '<?php echo $foo->bar[array_key_first($foo->baz)];',
            null,
        ];

        yield 'array_key_first when using a sub-attribute of an attribute' => [
            '<?php echo $foo->bar->baz[array_key_first($foo->bar)];',
            null,
        ];
    }
}
