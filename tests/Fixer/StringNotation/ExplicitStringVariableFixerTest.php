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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer
 */
final class ExplicitStringVariableFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): array
    {
        $input = $expected = '<?php';

        for ($inc = 1; $inc < 15; ++$inc) {
            $expected .= " \$var{$inc} = \"My name is {\$name}!\";";
            $input .= " \$var{$inc} = \"My name is \$name!\";";
        }

        return [
            [
                $expected,
                $input,
            ],
            [
                '<?php $a = "My name is {$name}!";',
                '<?php $a = "My name is $name!";',
            ],
            [
                '<?php "My name is {$james}{$bond}!";',
                '<?php "My name is $james$bond!";',
            ],
            [
                '<?php $a = <<<EOF
My name is {$name}!
EOF;
',
                '<?php $a = <<<EOF
My name is $name!
EOF;
',
            ],
            [
                '<?php $a = "{$b}";',
                '<?php $a = "$b";',
            ],
            [
                '<?php $a = "{$b} start";',
                '<?php $a = "$b start";',
            ],
            [
                '<?php $a = "end {$b}";',
                '<?php $a = "end $b";',
            ],
            [
                '<?php $a = <<<EOF
{$b}
EOF;
',
                '<?php $a = <<<EOF
$b
EOF;
',
            ],
            ['<?php $a = \'My name is $name!\';'],
            ['<?php $a = "My name is " . $name;'],
            ['<?php $a = "My name is {$name}!";'],
            [
                '<?php $a = <<<EOF
My name is {$name}!
EOF;
',
            ],
            ['<?php $a = "My name is {$user->name}";'],
            [
                '<?php $a = <<<EOF
My name is {$user->name}
EOF;
',
            ],
            [
                '<?php $a = <<<\'EOF\'
$b
EOF;
',
            ],
            [
                '<?php $a = "My name is {$object->property} !";',
                '<?php $a = "My name is $object->property !";',
            ],
            [
                '<?php $a = "My name is {$array[1]} !";',
                '<?php $a = "My name is $array[1] !";',
            ],
            [
                '<?php $a = "My name is {$array[\'foo\']} !";',
                '<?php $a = "My name is $array[foo] !";',
            ],
            [
                '<?php $a = "My name is {$array[$foo]} !";',
                '<?php $a = "My name is $array[$foo] !";',
            ],
            [
                '<?php $a = "My name is {$array[$foo]}[{$bar}] !";',
                '<?php $a = "My name is $array[$foo][$bar] !";',
            ],
            [
                '<?php $a = "Closure not allowed {$closure}() text";',
                '<?php $a = "Closure not allowed $closure() text";',
            ],
            [
                '<?php $a = "Complex object chaining not allowed {$object->property}->method()->array[1] text";',
                '<?php $a = "Complex object chaining not allowed $object->property->method()->array[1] text";',
            ],
            [
                '<?php $a = "Complex array chaining not allowed {$array[1]}[2][MY_CONSTANT] text";',
                '<?php $a = "Complex array chaining not allowed $array[1][2][MY_CONSTANT] text";',
            ],
            [
                '<?php $a = "Concatenation: {$james}{$bond}{$object->property}{$array[1]}!";',
                '<?php $a = "Concatenation: $james$bond$object->property$array[1]!";',
            ],
            [
                '<?php $a = "{$a->b} start";',
                '<?php $a = "$a->b start";',
            ],
            [
                '<?php $a = "end {$a->b}";',
                '<?php $a = "end $a->b";',
            ],
            [
                '<?php $a = "{$a[1]} start";',
                '<?php $a = "$a[1] start";',
            ],
            [
                '<?php $a = "end {$a[1]}";',
                '<?php $a = "end $a[1]";',
            ],
            [
                '<?php $a = b"{$a->b} start";',
                '<?php $a = b"$a->b start";',
            ],
            [
                '<?php $a = b"end {$a->b}";',
                '<?php $a = b"end $a->b";',
            ],
            [
                '<?php $a = b"{$a[1]} start";',
                '<?php $a = b"$a[1] start";',
            ],
            [
                '<?php $a = b"end {$a[1]}";',
                '<?php $a = b"end $a[1]";',
            ],
            [
                '<?php $a = B"{$a->b} start";',
                '<?php $a = B"$a->b start";',
            ],
            [
                '<?php $a = B"end {$a->b}";',
                '<?php $a = B"end $a->b";',
            ],
            [
                '<?php $a = B"{$a[1]} start";',
                '<?php $a = B"$a[1] start";',
            ],
            [
                '<?php $a = B"end {$a[1]}";',
                '<?php $a = B"end $a[1]";',
            ],
            [
                '<?php $a = "*{$a[0]}{$b[1]}X{$c[2]}{$d[3]}";',
                '<?php $a = "*$a[0]$b[1]X$c[2]$d[3]";',
            ],
            [
                '<?php $a = `echo $foo`;',
            ],
            [
                '<?php $a = "My name is {$name}!"; $a = `echo $foo`; $a = "{$a->b} start";',
                '<?php $a = "My name is $name!"; $a = `echo $foo`; $a = "$a->b start";',
            ],
            [
                '<?php $mobileNumberVisible = "***-***-{$last4Digits[0]}{$last4Digits[1]}-{$last4Digits[2]}{$last4Digits[3]}";',
                '<?php $mobileNumberVisible = "***-***-$last4Digits[0]$last4Digits[1]-$last4Digits[2]$last4Digits[3]";',
            ],
            [
                '<?php $pair = "{$foo} {$bar[0]}";',
                '<?php $pair = "$foo {$bar[0]}";',
            ],
            [
                '<?php $pair = "{$foo}{$bar[0]}";',
                '<?php $pair = "$foo{$bar[0]}";',
            ],
            [
                '<?php $a = "My name is {$array[-1]} !";',
                '<?php $a = "My name is $array[-1] !";',
            ],
            [
                '<?php $a = "{$a[-1]} start";',
                '<?php $a = "$a[-1] start";',
            ],
            [
                '<?php $a = "end {$a[-1]}";',
                '<?php $a = "end $a[-1]";',
            ],
            [
                '<?php $a = b"end {$a[-1]}";',
                '<?php $a = b"end $a[-1]";',
            ],
            [
                '<?php $a = B"end {$a[-1]}";',
                '<?php $a = B"end $a[-1]";',
            ],
        ];
    }
}
