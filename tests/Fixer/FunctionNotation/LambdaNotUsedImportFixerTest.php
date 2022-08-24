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
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            'simple' => [
                '<?php $foo = function() {};',
                '<?php $foo = function() use ($bar) {};',
            ],
            'simple, one of two' => [
                '<?php $foo = function & () use ( $foo) { echo $foo; };',
                '<?php $foo = function & () use ($bar, $foo) { echo $foo; };',
            ],
            'simple, one used, one reference, two not used' => [
                '<?php $foo = function() use ($bar, &$foo  ) { echo $bar; };',
                '<?php $foo = function() use ($bar, &$foo, $not1, $not2) { echo $bar; };',
            ],
            'simple, but witch comments' => [
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
            ],
            'nested lambda I' => [
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
            ],
            'nested lambda II' => [
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
            ],
            'anonymous class' => [
                '<?php
$a = function() use ($b) { new class($b){}; }; // do not fix
$a = function() { new class(){ public function foo($b){echo $b;}}; }; // do fix
',
                '<?php
$a = function() use ($b) { new class($b){}; }; // do not fix
$a = function() use ($b) { new class(){ public function foo($b){echo $b;}}; }; // do fix
',
            ],
        ];
    }

    /**
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideDoNotFixCases(): iterable
    {
        yield from [
            'reference' => [
                '<?php $fn = function() use(&$b) {} ?>',
            ],
            'compact 1' => [
                '<?php $foo = function() use ($b) { return compact(\'b\'); };',
            ],
            'compact 2' => [
                '<?php $foo = function() use ($b) { return \compact(\'b\'); };',
            ],
            'eval' => [
                '<?php $foo = function($c) use ($b) { eval($c); };',
            ],
            'include' => [
                '<?php $foo = function($c) use ($b) { include __DIR__."/test3.php"; };',
            ],
            'include_once' => [
                '<?php $foo = function($c) use ($b) { include_once __DIR__."/test3.php"; };',
            ],
            'require' => [
                '<?php $foo = function($c) use ($b) { require __DIR__."/test3.php"; };',
            ],
            'require_once' => [
                '<?php $foo = function($c) use ($b) { require_once __DIR__."/test3.php"; };',
            ],
            '${X}' => [
                '<?php $foo = function($g) use ($b) { $h = ${$g}; };',
            ],
            '$$c' => [
                '<?php $foo = function($g) use ($b) { $h = $$g; };',
            ],
            'interpolation 1' => [
                '<?php $foo = function() use ($world) { echo "hello $world"; };',
            ],
            'interpolation 2' => [
                '<?php $foo = function() use ($world) { echo "hello {$world}"; };',
            ],
            'interpolation 3' => [
                '<?php $foo = function() use ($world) { echo "hello ${world}"; };',
            ],
            'heredoc' => [
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
            ],
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

    public function provideFix80Cases(): iterable
    {
        yield 'simple' => [
            '<?php $foo = function() {};',
            '<?php $foo = function() use ($bar,) {};',
        ];
    }
}
