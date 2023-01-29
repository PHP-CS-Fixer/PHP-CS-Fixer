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
use PhpCsFixer\Tokenizer\Tokens;

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

    public static function provideFixCases(): array
    {
        return [
            ['<?php class Test { public function __construct() {} }'],
            ['<?php class Test { public function __destruct() {} }'],
            ['<?php class Test { public function __clone() {} }'],
            ['<?php function foo($param) { return $param; }'],
            ['<?php function foo($param) { return null; }'],
            ['<?php function foo($param) { yield; }'],
            ['<?php function foo($param) { yield $param; }'],
            ['<?php function foo($param) { yield from test(); }'],
            ['<?php function foo($param): Void {}'],
            ['<?php interface Test { public function foo($param); }'],
            ['<?php function foo($param) { return function($a) use ($param): string {}; }'],
            ['<?php abstract class Test { abstract public function foo($param); }'],
            ['<?php use Foo\ { function Bar }; function test() { return Bar(); }'],
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
                '<?php $arr = []; usort($arr, function ($a, $b): void {});',
                '<?php $arr = []; usort($arr, function ($a, $b) {});',
            ],
            [
                '<?php $arr = []; $param = 1; usort($arr, function ($a, $b) use ($param): void {});',
                '<?php $arr = []; $param = 1; usort($arr, function ($a, $b) use ($param) {});',
            ],
            [
                '<?php function foo($param) { return function($a) use ($param): void {}; }',
                '<?php function foo($param) { return function($a) use ($param) {}; }',
            ],
            [
                '<?php function foo($param): void { $arr = []; usort($arr, function ($a, $b) use ($param): void {}); }',
                '<?php function foo($param) { $arr = []; usort($arr, function ($a, $b) use ($param) {}); }',
            ],
            [
                '<?php function foo() { $arr = []; return usort($arr, new class { public function __invoke($a, $b): void {} }); }',
                '<?php function foo() { $arr = []; return usort($arr, new class { public function __invoke($a, $b) {} }); }',
            ],
            [
                '<?php function foo(): void { $arr = []; usort($arr, new class { public function __invoke($a, $b): void {} }); }',
                '<?php function foo() { $arr = []; usort($arr, new class { public function __invoke($a, $b) {} }); }',
            ],
            [
                '<?php
                function foo(): void {
                    $a = function (): void {};
                }',
                '<?php
                function foo() {
                    $a = function () {};
                }',
            ],
            [
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
            ],
            [
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
            ],
            [
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
                    abstract protected function foo($param): void;
                }',

                '<?php
                abstract class Test {
                    /**
                     * @return void
                     */
                    abstract protected function foo($param);
                }',
            ],
            [
                '<?php fn($a) => null;',
            ],
            [
                '<?php fn($a) => 1;',
            ],
            [
                '<?php fn($a) => var_dump($a);',
            ],
        ];
    }

    /**
     * Test if magic method is handled without causing syntax error.
     *
     * @dataProvider provideMethodWillNotCauseSyntaxErrorCases
     */
    public function testMethodWillNotCauseSyntaxError(string $method, int $arguments = 0, bool $static = false): void
    {
        $tokens = Tokens::fromCode(sprintf(
            '<?php class Test { public%s function %s(%s) {} }',
            $static ? ' static' : '',
            $method,
            implode(',', array_map(
                static fn (int $n): string => sprintf('$x%d', $n),
                array_keys(array_fill(0, $arguments, true)),
            ))
        ));

        $this->fixer->fix($this->getTestFile(), $tokens);

        static::assertNull($this->lintSource($tokens->generateCode()));
    }

    public static function provideMethodWillNotCauseSyntaxErrorCases(): iterable
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
