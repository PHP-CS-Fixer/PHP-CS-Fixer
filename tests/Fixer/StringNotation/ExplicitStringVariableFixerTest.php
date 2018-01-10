<?php

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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        $input = $expected = '<?php';
        for ($inc = 1; $inc < 15; ++$inc) {
            $input .= " \$var${inc} = \"My name is \$name!\";";
            $expected .= " \$var${inc} = \"My name is \${name}!\";";
        }

        return [
            [
                $expected,
                $input,
            ],
            [
                '<?php $a = "My name is ${name}!";',
                '<?php $a = "My name is $name!";',
            ],
            [
'<?php $a = <<<EOF
My name is ${name}!
EOF;
',
'<?php $a = <<<EOF
My name is $name!
EOF;
',
            ],
            [
                '<?php $a = "${b}";',
                '<?php $a = "$b";',
            ],
            [
                '<?php $a = "${b} start";',
                '<?php $a = "$b start";',
            ],
            [
                '<?php $a = "end ${b}";',
                '<?php $a = "end $b";',
            ],
            [
'<?php $a = <<<EOF
${b}
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
                '<?php $a = "My name is {$array[MY_CONSTANT]} !";',
                '<?php $a = "My name is $array[MY_CONSTANT] !";',
            ],
            [
                '<?php $a = "Closure not allowed ${closure}() text";',
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
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFix71Cases
     * @requires PHP 7.1
     */
    public function testFix71($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFix71Cases()
    {
        return [
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
        ];
    }
}
