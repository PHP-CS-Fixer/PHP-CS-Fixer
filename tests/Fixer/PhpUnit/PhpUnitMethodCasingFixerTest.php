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
     * @dataProvider provideCamelCaseSnakeCaseFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixToCamelCase($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideCamelCaseSnakeCaseFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixToSnakeCase($expected, $input = null)
    {
        $this->fixer->configure(['case' => PhpUnitMethodCasingFixer::SNAKE_CASE]);

        if (null === $input) {
            $this->doTest($expected, $input);
        } else {
            $this->doTest($input, $expected);
        }
    }

    public function provideCamelCaseSnakeCaseFixCases()
    {
        foreach ($this->getCases() as $name => $case) {
            if (!isset($case[1])) {
                yield $name => $case;

                continue;
            }

            yield $name => [$case[0], $case[1]];
        }
    }

    /**
     * @dataProvider provideCamelCaseNonBreakingSpacesFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixFromNonBreakingSpacesToCamelCase($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideCamelCaseNonBreakingSpacesFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixFromCamelCaseToNonBreakingSpaces($expected, $input = null)
    {
        $this->fixer->configure(['case' => PhpUnitMethodCasingFixer::NON_BREAKING_SPACES]);

        if (null === $input) {
            $this->doTest($expected, $input);
        } else {
            $this->doTest($input, $expected);
        }
    }

    public function provideCamelCaseNonBreakingSpacesFixCases()
    {
        foreach ($this->getCases() as $name => $case) {
            if (!isset($case[1])) {
                yield $name => $case;

                continue;
            }

            yield $name => [$case[0], $case[2]];
        }
    }

    /**
     * @dataProvider provideSnakeCaseNonBreakingSpacesFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixFromNonBreakingSpacesToSnakeCase($expected, $input = null)
    {
        $this->fixer->configure(['case' => PhpUnitMethodCasingFixer::SNAKE_CASE]);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideSnakeCaseNonBreakingSpacesFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixFromSnakeCaseToNonBreakingSpaces($expected, $input = null)
    {
        $this->fixer->configure(['case' => PhpUnitMethodCasingFixer::NON_BREAKING_SPACES]);

        if (null === $input) {
            $this->doTest($expected, $input);
        } else {
            $this->doTest($input, $expected);
        }
    }

    public function provideSnakeCaseNonBreakingSpacesFixCases()
    {
        foreach ($this->getCases() as $name => $case) {
            if (!isset($case[1])) {
                yield $name => $case;

                continue;
            }

            yield $name => [$case[1], $case[2]];
        }
    }

    /**
     * @return array
     */
    private function getCases()
    {
        $nbsp = pack('H*', 'c2a0');

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
                "<?php class MyTest extends \\PhpUnit\\FrameWork\\TestCase { public function test{$nbsp}my{$nbsp}app() {} }",
            ],
            'annotation' => [
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function myApp() {} }',
                '<?php class MyTest extends \PhpUnit\FrameWork\TestCase { /** @test */ public function my_app() {} }',
                "<?php class MyTest extends \\PhpUnit\\FrameWork\\TestCase { /** @test */ public function my{$nbsp}app() {} }",
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
                "<?php class MyTest extends \\PhpUnit\\FrameWork\\TestCase {
                    public function test{$nbsp}my{$nbsp}app () {}

                    /**
                     * @depends test{$nbsp}my{$nbsp}app
                     */
                    public function test{$nbsp}my{$nbsp}app{$nbsp}too() {}
                }",
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
                "<?php class MyTest extends \\PhpUnit\\FrameWork\\TestCase {
                    /**
                     * @test
                     */
                    public function my{$nbsp}app () {}

                    /**
                     * @test
                     * @depends my{$nbsp}app
                     */
                    public function my{$nbsp}app{$nbsp}too() {}
                }",
            ],
        ];
    }
}
