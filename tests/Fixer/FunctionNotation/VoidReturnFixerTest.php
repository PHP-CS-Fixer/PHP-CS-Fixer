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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Mark Nielsen
 *
 * @internal
 *
 * @requires PHP 7.1
 * @covers   \PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer
 */
final class VoidReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            ['<?php class Test { public function __construct() {} }'],
            ['<?php class Test { public function __destruct() {} }'],
            ['<?php class Test { public function __clone() {} }'],
            ['<?php function foo($param) { return $param; }'],
            ['<?php function foo($param) { return null; }'],
            ['<?php function foo($param) { yield; }'],
            ['<?php function foo($param) { yield $param; }'],
            ['<?php function foo($param): Void {}'],
            ['<?php interface Test { public function foo($param); }'],
            ['<?php function foo($param) { return function($a) use ($param): string {}; }'],
            ['<?php abstract class Test { abstract public function foo($param); }'],
            ['<?php
                /**
                 * @return array
                 */
                function foo($param) {}
            '],
            ['<?php
                interface Test {
                    /**
                     * @return array
                     */
                    public function foo($param);
                }
            '],
            [
                '<?php function foo($param): void { return; }',
                '<?php function foo($param) { return; }',
            ],
            [
                '<?php function foo($param): void {}',
                '<?php function foo($param) {}',
            ],
            [
                '<?php class Test { public function foo($param): void { return; } }',
                '<?php class Test { public function foo($param) { return; } }',
            ],
            [
                '<?php class Test { public function foo($param): void {} }',
                '<?php class Test { public function foo($param) {} }',
            ],
            [
                '<?php trait Test { public function foo($param): void { return; } }',
                '<?php trait Test { public function foo($param) { return; } }',
            ],
            [
                '<?php trait Test { public function foo($param): void {} }',
                '<?php trait Test { public function foo($param) {} }',
            ],
            [
                '<?php usort([], function ($a, $b): void {});',
                '<?php usort([], function ($a, $b) {});',
            ],
            [
                '<?php $param = 1; usort([], function ($a, $b) use ($param): void {});',
                '<?php $param = 1; usort([], function ($a, $b) use ($param) {});',
            ],
            [
                '<?php function foo($param) { return function($a) use ($param): void {}; }',
                '<?php function foo($param) { return function($a) use ($param) {}; }',
            ],
            [
                '<?php function foo($param): void { usort([], function ($a, $b) use ($param): void {}); }',
                '<?php function foo($param) { usort([], function ($a, $b) use ($param) {}); }',
            ],
            [
                '<?php function foo() { return usort([], new class { public function __invoke($a, $b): void {} }); }',
                '<?php function foo() { return usort([], new class { public function __invoke($a, $b) {} }); }',
            ],
            [
                '<?php function foo(): void { usort([], new class { public function __invoke($a, $b): void {} }); }',
                '<?php function foo() { usort([], new class { public function __invoke($a, $b) {} }); }',
            ],
            [
                '<?php
                /**
                 * @return void
                 */
                function foo($param): void {}',

                '<?php
                /**
                 * @return void
                 */
                function foo($param) {}',
            ],
            [
                '<?php
                interface Test {
                    /**
                     * @return void
                     */
                    public function foo($param): void;
                }',

                '<?php
                interface Test {
                    /**
                     * @return void
                     */
                    public function foo($param);
                }',
            ],
            [
                '<?php
                abstract class Test {
                    /**
                     * @return void
                     */
                    abstract private function foo($param): void;
                }',

                '<?php
                abstract class Test {
                    /**
                     * @return void
                     */
                    abstract private function foo($param);
                }',
            ],
        ];
    }
}
