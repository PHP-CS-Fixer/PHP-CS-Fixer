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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Fixer\Casing\VariableCaseFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Jennifer Konikowski <jennifer@testdouble.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\VariableCaseFixer
 */
final class VariableCaseFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCamelCaseFixCases
     */
    public function testCamelCaseFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCamelCaseFixCases()
    {
        return [
            [
                '<?php $testVariable = 2;',
                '<?php $test_variable = 2;',
            ],
            [
                '<?php $testVariable = 2; echo "hi $testVariable!";',
                '<?php $test_variable = 2; echo "hi $test_variable!";',
            ],
            [
                '<?php $testVariable = 2; echo "hi ${testVariable}!";',
                '<?php $test_variable = 2; echo "hi ${test_variable}!";',
            ],
            [
                '<?php $testVariable = 2; echo "hi {$testVariable}!";',
                '<?php $test_variable = 2; echo "hi {$test_variable}!";',
            ],
            [
                '<?php function foo_bar() { $testVariable = 2;}',
                '<?php function foo_bar() { $test__variable = 2;}',
            ],
            [
                '<?php echo $testModel->this_field;',
                '<?php echo $test_model->this_field;',
            ],
            [
                '<?php function f($barBaz, $file) { require $file;}',
                '<?php function f($bar_baz, $file) { require $file;}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideSnakeCaseFixCases
     */
    public function testSnakeCaseFix($expected, $input = null)
    {
        $this->fixer->configure(['case' => VariableCaseFixer::SNAKE_CASE]);
        $this->doTest($expected, $input);
    }

    public function provideSnakeCaseFixCases()
    {
        return [
            [
                '<?php $test_variable = 2;',
                '<?php $testVariable = 2;',
            ],
            [
                '<?php $abc_12_variable = 2;',
                '<?php $abc12Variable = 2;',
            ],
            [
                '<?php $abc_123_a_variable = 2;',
                '<?php $abc123aVariable = 2;',
            ],
            [
                '<?php function fooBar() { $test_variable = 2;}',
                '<?php function fooBar() { $testVariable = 2;}',
            ],
            [
                '<?php $test_variable = 2; echo "hi $test_variable!";',
                '<?php $testVariable = 2; echo "hi $testVariable!";',
            ],
            [
                '<?php $test_variable = 2; echo "hi ${test_variable}!";',
                '<?php $testVariable = 2; echo "hi ${testVariable}!";',
            ],
            [
                '<?php $test_variable = 2; echo "hi {$test_variable}!";',
                '<?php $testVariable = 2; echo "hi {$testVariable}!";',
            ],
            [
                '<?php echo $test_model->this_field;',
                '<?php echo $testModel->this_field;',
            ],
            [
                '<?php function f($bar_baz, $file) { require $file;}',
                '<?php function f($barBaz, $file) { require $file;}',
            ],
        ];
    }
}
