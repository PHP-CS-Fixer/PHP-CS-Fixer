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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Mark Nielsen
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer
 */
final class VoidReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: ?string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php class Test { public function __construct() {} }'];

        yield ['<?php class Test { public function __destruct() {} }'];

        yield ['<?php class Test { public function __clone() {} }'];

        yield ['<?php function foo($param) { return $param; }'];

        yield ['<?php function foo($param) { return null; }'];

        yield ['<?php function foo($param) { yield; }'];

        yield ['<?php function foo($param) { yield $param; }'];

        yield ['<?php function foo($param) { yield from test(); }'];

        yield ['<?php function foo($param): Void {}'];

        yield ['<?php interface Test { public function foo($param); }'];

        yield ['<?php function foo($param) { return function($a) use ($param): string {}; }'];

        yield ['<?php abstract class Test { abstract public function foo($param); }'];

        yield ['<?php use Foo\ { function Bar }; function test() { return Bar(); }'];

        yield ['<?php
                /**
                 * @return array
                 */
                function foo($param) {}
            '];

        yield ['<?php
                interface Test {
                    /**
                     * @return array
                     */
                    public function foo($param);
                }
            '];

        yield [
            '<?php function foo($param): void { return; }',
            '<?php function foo($param) { return; }',
        ];

        yield [
            '<?php function foo($param): void {}',
            '<?php function foo($param) {}',
        ];

        yield [
            '<?php class Test { public function foo($param): void { return; } }',
            '<?php class Test { public function foo($param) { return; } }',
        ];

        yield [
            '<?php class Test { public function foo($param): void {} }',
            '<?php class Test { public function foo($param) {} }',
        ];

        yield [
            '<?php trait Test { public function foo($param): void { return; } }',
            '<?php trait Test { public function foo($param) { return; } }',
        ];

        yield [
            '<?php trait Test { public function foo($param): void {} }',
            '<?php trait Test { public function foo($param) {} }',
        ];

        yield [
            '<?php $arr = []; usort($arr, function ($a, $b): void {});',
            '<?php $arr = []; usort($arr, function ($a, $b) {});',
        ];

        yield [
            '<?php $arr = []; $param = 1; usort($arr, function ($a, $b) use ($param): void {});',
            '<?php $arr = []; $param = 1; usort($arr, function ($a, $b) use ($param) {});',
        ];

        yield [
            '<?php function foo($param) { return function($a) use ($param): void {}; }',
            '<?php function foo($param) { return function($a) use ($param) {}; }',
        ];

        yield [
            '<?php function foo($param): void { $arr = []; usort($arr, function ($a, $b) use ($param): void {}); }',
            '<?php function foo($param) { $arr = []; usort($arr, function ($a, $b) use ($param) {}); }',
        ];

        yield [
            '<?php function foo() { $arr = []; return usort($arr, new class { public function __invoke($a, $b): void {} }); }',
            '<?php function foo() { $arr = []; return usort($arr, new class { public function __invoke($a, $b) {} }); }',
        ];

        yield [
            '<?php function foo(): void { $arr = []; usort($arr, new class { public function __invoke($a, $b): void {} }); }',
            '<?php function foo() { $arr = []; usort($arr, new class { public function __invoke($a, $b) {} }); }',
        ];

        yield [
            '<?php
                function foo(): void {
                    $a = function (): void {};
                }',
            '<?php
                function foo() {
                    $a = function () {};
                }',
        ];

        yield [
            '<?php
                function foo(): void {
                    (function (): void {
                        return;
                    })();
                }',
            '<?php
                function foo() {
                    (function () {
                        return;
                    })();
                }',
        ];

        yield [
            '<?php
                function foo(): void {
                    (function () {
                        return 1;
                    })();
                }',
            '<?php
                function foo() {
                    (function () {
                        return 1;
                    })();
                }',
        ];

        yield [
            '<?php
                function foo(): void {
                    $b = new class {
                        public function b1(): void {}
                        public function b2() { return 2; }
                    };
                }',
            '<?php
                function foo() {
                    $b = new class {
                        public function b1() {}
                        public function b2() { return 2; }
                    };
                }',
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            '<?php
                abstract class Test {
                    /**
                     * @return void
                     */
                    abstract protected function foo($param): void;
                }',

            '<?php
                abstract class Test {
                    /**
                     * @return void
                     */
                    abstract protected function foo($param);
                }',
        ];

        yield [
            '<?php fn($a) => null;',
        ];

        yield [
            '<?php fn($a) => 1;',
        ];

        yield [
            '<?php fn($a) => var_dump($a);',
        ];

        $excluded = ['__clone', '__construct', '__debugInfo', '__destruct', '__isset', '__serialize', '__set_state', '__sleep', '__toString'];

        foreach (self::provideMagicMethodsDefinitions() as $magicMethodsDefinition) {
            $name = $magicMethodsDefinition[0];
            $arguments = $magicMethodsDefinition[1] ?? 0;
            $isStatic = $magicMethodsDefinition[2] ?? false;
            $code = sprintf(
                '<?php class Test { public%s function %s(%s)%%s {} }',
                $isStatic ? ' static' : '',
                $name,
                implode(',', array_map(
                    static fn (int $n): string => sprintf('$x%d', $n),
                    array_keys(array_fill(0, $arguments ?? 0, true)),
                ))
            );

            $input = sprintf($code, '');
            $expected = sprintf($code, \in_array($name, $excluded, true) ? '' : ': void');

            yield sprintf('Test if magic method %s is handled without causing syntax error', $name) => [
                $expected,
                $expected === $input ? null : $input,
            ];
        }
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, 1?: ?string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php

            class Foo
            {
                /**
                 * @return int|false
                 */
                #[\ReturnTypeWillChange]
                public function test() {}
            }
            ',
        ];

        yield [
            '<?php

            /**
             * @return void
             */
            #[\Deprecated]
            function test(): void {};
            ',
            '<?php

            /**
             * @return void
             */
            #[\Deprecated]
            function test() {};
            ',
        ];
    }

    /**
     * @return iterable<array{string, 1?: int, 2?: bool}>
     */
    private static function provideMagicMethodsDefinitions(): iterable
    {
        // List: https://www.php.net/manual/en/language.oop5.magic.php
        yield ['__construct'];

        yield ['__destruct'];

        yield ['__call', 2];

        yield ['__callStatic', 2, true];

        yield ['__get', 1];

        yield ['__set', 2];

        yield ['__isset', 1];

        yield ['__unset', 1];

        yield ['__sleep'];

        yield ['__wakeup'];

        yield ['__serialize'];

        yield ['__unserialize', 1];

        yield ['__toString'];

        yield ['__invoke'];

        yield ['__set_state', 1, true];

        yield ['__clone'];

        yield ['__debugInfo'];
    }
}
