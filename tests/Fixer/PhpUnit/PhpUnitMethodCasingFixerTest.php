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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer
 */
final class PhpUnitMethodCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixToCamelCase($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCases
     *
     * @param mixed      $camelExpected
     * @param null|mixed $camelInput
     */
    public function testFixToSnakeCase($camelExpected, $camelInput = null)
    {
        if (null === $camelInput) {
            $expected = $camelExpected;
            $input = $camelInput;
        } else {
            $expected = $camelInput;
            $input = $camelExpected;
        }

        $this->fixer->configure(['case' => PhpUnitMethodCasingFixer::SNAKE_CASE]);
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            'skip non phpunit methods' => [
                '<?php class MyClass {
                    public function testMyApp() {}
                    public function test_my_app() {}
                }',
            ],
            'skip non test methods' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function not_a_test() {}
                    public function notATestEither() {}
                }',
            ],
            'default sample' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { public function testMyApp() {} }',
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { public function test_my_app() {} }',
            ],
            'annotation' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function myApp() {} }',
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function my_app() {} }',
            ],
            '@depends annotation' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function testMyApp () {}

                    /**
                     * @depends testMyApp
                     */
                    public function testMyAppToo() {}
                }',
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    public function test_my_app () {}

                    /**
                     * @depends test_my_app
                     */
                    public function test_my_app_too() {}
                }',
            ],
            '@depends and @test annotation' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    /**
                     * @test
                     */
                    public function myApp () {}

                    /**
                     * @test
                     * @depends myApp
                     */
                    public function myAppToo() {}
                }',
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase {
                    /**
                     * @test
                     */
                    public function my_app () {}

                    /**
                     * @test
                     * @depends my_app
                     */
                    public function my_app_too() {}
                }',
            ],
        ];
    }
}
