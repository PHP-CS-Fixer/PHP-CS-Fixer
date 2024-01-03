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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\LambdaNotUsedImportFixer
 */
final class LambdaNotUsedImportFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'simple' => [
            '<?php $foo = function() {};',
            '<?php $foo = function() use ($bar) {};',
        ];

        yield 'simple, one of two' => [
            '<?php $foo = function & () use ( $foo) { echo $foo; };',
            '<?php $foo = function & () use ($bar, $foo) { echo $foo; };',
        ];

        yield 'simple, one used, one reference, two not used' => [
            '<?php $foo = function() use ($bar, &$foo  ) { echo $bar; };',
            '<?php $foo = function() use ($bar, &$foo, $not1, $not2) { echo $bar; };',
        ];

        yield 'simple, but witch comments' => [
            '<?php $foo = function()
# 1
#2
# 3
#4
# 5
 #6
{};',
            '<?php $foo = function()
use
# 1
( #2
# 3
$bar #4
# 5
) #6
{};',
        ];

        yield 'nested lambda I' => [
            '<?php

$f = function() {
    return function ($d) use ($c) {
        $b = 1; echo $c;
    };
};
',
            '<?php

$f = function() use ($b) {
    return function ($d) use ($c) {
        $b = 1; echo $c;
    };
};
',
        ];

        yield 'nested lambda II' => [
            '<?php
// do not fix
$f = function() use ($a) { return function() use ($a) { return function() use ($a) { return function() use ($a) { echo $a; }; }; }; };
$f = function() use ($b) { return function($b) { return function($b) { return function($b) { echo $b; }; }; }; };

// do fix
$f = function() { return function() { return function() { return function() { }; }; }; };
                ',
            '<?php
// do not fix
$f = function() use ($a) { return function() use ($a) { return function() use ($a) { return function() use ($a) { echo $a; }; }; }; };
$f = function() use ($b) { return function($b) { return function($b) { return function($b) { echo $b; }; }; }; };

// do fix
$f = function() use ($a) { return function() use ($a) { return function() use ($a) { return function() use ($a) { }; }; }; };
                ',
        ];

        yield 'anonymous class' => [
            '<?php
$a = function() use ($b) { new class($b){}; }; // do not fix
$a = function() { new class(){ public function foo($b){echo $b;}}; }; // do fix
',
            '<?php
$a = function() use ($b) { new class($b){}; }; // do not fix
$a = function() use ($b) { new class(){ public function foo($b){echo $b;}}; }; // do fix
',
        ];

        yield 'anonymous class with a string argument' => [
            '<?php $function = function () {
                    new class("bar") {};
                };',
            '<?php $function = function () use ($foo) {
                    new class("bar") {};
                };',
        ];

        yield 'reference' => [
            '<?php $fn = function() use(&$b) {} ?>',
        ];

        yield 'compact 1' => [
            '<?php $foo = function() use ($b) { return compact(\'b\'); };',
        ];

        yield 'compact 2' => [
            '<?php $foo = function() use ($b) { return \compact(\'b\'); };',
        ];

        yield 'eval' => [
            '<?php $foo = function($c) use ($b) { eval($c); };',
        ];

        yield 'include' => [
            '<?php $foo = function($c) use ($b) { include __DIR__."/test3.php"; };',
        ];

        yield 'include_once' => [
            '<?php $foo = function($c) use ($b) { include_once __DIR__."/test3.php"; };',
        ];

        yield 'require' => [
            '<?php $foo = function($c) use ($b) { require __DIR__."/test3.php"; };',
        ];

        yield 'require_once' => [
            '<?php $foo = function($c) use ($b) { require_once __DIR__."/test3.php"; };',
        ];

        yield '${X}' => [
            '<?php $foo = function($g) use ($b) { $h = ${$g}; };',
        ];

        yield '$$c' => [
            '<?php $foo = function($g) use ($b) { $h = $$g; };',
        ];

        yield 'interpolation 1' => [
            '<?php $foo = function() use ($world) { echo "hello $world"; };',
        ];

        yield 'interpolation 2' => [
            '<?php $foo = function() use ($world) { echo "hello {$world}"; };',
        ];

        yield 'interpolation 3' => [
            '<?php $foo = function() use ($world) { echo "hello ${world}"; };',
        ];

        yield 'heredoc' => [
            '<?php
$b = 123;
$foo = function() use ($b) {
    echo
<<<"TEST"
Foo $b
TEST;
};

$foo();
',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'simple' => [
            '<?php $foo = function() {};',
            '<?php $foo = function() use ($bar,) {};',
        ];
    }
}
