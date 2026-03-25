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
        foreach ([['array_key_last', 'array_last'], ['array_key_first', 'array_first']] as [$keyFunction, $function]) {
            foreach (['$foo', "\$foo['bar']['baz']", '$foo[$bar]', '$foo->$bar', '$foo->bar->$bar', '$foo->bar->baz', 'MY_CONST', 'Foo::MY_CONST', 'Foo::$bar'] as $expression) {
                yield "normal {$keyFunction}, expression {$expression}" => [
                    "<?php echo {$function}({$expression});",
                    "<?php echo {$expression}[{$keyFunction}({$expression})];",
                ];

                yield "{$keyFunction} on right-hand side of assignment, expression {$expression}" => [
                    "<?php \$bar = {$function}({$expression});",
                    "<?php \$bar = {$expression}[{$keyFunction}({$expression})];",
                ];

                // negative tests for left-hand side of assignment
                yield "{$keyFunction} on left-hand side of assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] = 0;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of and-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] &= false;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of coalesce-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] ??= false;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of concat-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] .= 'd';",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of div-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] /= 2;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of minus-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] -= 2;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of mod-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] %= 2;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of multiply-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] *= 2;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of or-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] |= false;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of left-shift-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] <<= 2;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of right-shift-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}({$expression})] >>= 2;",
                    null,
                ];

                yield "{$keyFunction} on left-hand side of xor-equal assignment, expression {$expression}" => [
                    "<?php {$expression}[{$keyFunction}(\$foo)] ^= false;",
                    null,
                ];
            }

            yield "{$keyFunction} when using a function as an expression" => [
                "<?php echo range(1,5)[{$keyFunction}(range(1,5))];",
                null,
            ];

            yield "{$keyFunction} when using a method as an expression" => [
                "<?php echo \$foo->bar()[{$keyFunction}(\$foo->bar())];",
                null,
            ];

            yield "{$keyFunction} when using a static function as an expression" => [
                "<?php echo Foo::bar()[{$keyFunction}(Foo::bar())];",
                null,
            ];

            yield "{$keyFunction} when using different variables" => [
                "<?php echo \$foo[{$keyFunction}(\$bar)];",
                null,
            ];

            yield "{$keyFunction} when using different attributes" => [
                "<?php echo \$foo->bar[{$keyFunction}(\$foo->baz)];",
                null,
            ];

            yield "{$keyFunction} when using a sub-attribute of an attribute" => [
                "<?php echo \$foo->bar->baz[{$keyFunction}(\$foo->bar)];",
                null,
            ];
        }
    }
}
