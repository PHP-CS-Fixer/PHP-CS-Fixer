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
        return [
            [
                '<?php $a = "My name is ${name}!";',
                '<?php $a = "My name is $name!";',
            ],
            [
                '<?php $a = "${b}";',
                '<?php $a = "$b";',
            ],
            ['<?php $a = \'My name is $name!\';'],
            ['<?php $a = "My name is " . $name;'],
            ['<?php $a = "My name is {$name}!";'],
            ['<?php $a = "My name is {$user->name}";'],
        ];
    }
}
