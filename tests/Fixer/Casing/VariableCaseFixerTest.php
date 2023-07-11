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
     * @dataProvider provideIgnoreCases
     */
    public function testIgnore(string $input, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($input);
    }

    public static function provideIgnoreCases(): iterable
    {
        foreach (['public', 'protected', 'private'] as $visibility) {
            $modifiers = ['', 'static'];

            if (\defined('T_READONLY')) { // @TODO: drop condition when PHP 8.1+ is required
                $modifiers[] = 'readonly';
            }

            foreach ($modifiers as $modifier) {
                yield "{$visibility} {$modifier} property snake_case" => ["<?php class Foo { {$visibility} {$modifier} \$bar_baz; }"];

                yield "{$visibility} {$modifier} property camelCase" => [
                    "<?php class Foo { {$visibility} {$modifier} \$barBaz; }",
                    ['case' => VariableCaseFixer::SNAKE_CASE],
                ];
            }
        }
    }

    /**
     * @dataProvider provideCamelCaseFixCases
     */
    public function testCamelCaseFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideCamelCaseFixCases(): iterable
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
            [
                '<?php function f($bar_baz, $file) { require $file;}',
            ],
        ];
    }

    /**
     * @dataProvider provideSnakeCaseFixCases
     */
    public function testSnakeCaseFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['case' => VariableCaseFixer::SNAKE_CASE]);
        $this->doTest($expected, $input);
    }

    public static function provideSnakeCaseFixCases(): iterable
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
