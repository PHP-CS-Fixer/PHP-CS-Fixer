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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\BlankLinesInsideBlockFixer
 */
final class BlankLinesInsideBlockFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield 'class' => [
            '<?php
class Foo {
    public function foo() {
    }
}',
            '<?php
class Foo {

    public function foo() {
    }

}',
        ];

        yield 'interface' => [
            '<?php
interface Foo {
    public function foo();
}',
            '<?php
interface Foo {

    public function foo();

}',
        ];

        yield 'trait' => [
            '<?php
trait Foo {
    public function foo() {
    }
}',
            '<?php
trait Foo {

    public function foo() {
    }

}',
        ];

        yield 'method' => [
            '<?php
class Foo {
    public function foo() {
        if ($foo == true) {
            echo "foo";
        }
    }
}',
            '<?php
class Foo {
    public function foo() {

        if ($foo == true) {
            echo "foo";
        }

    }
}',
        ];

        yield 'function' => [
            '<?php
function foo() {
    if ($foo == true) {
        echo "foo";
    }
}',
            '<?php
function foo() {

    if ($foo == true) {
        echo "foo";
    }

}',
        ];

        yield 'anonymous function' => [
            '<?php
$foo = function() {
    if ($foo == true) {
        echo "foo";
    }
};',
            '<?php
$foo = function() {

    if ($foo == true) {
        echo "foo";
    }

};',
        ];

        yield 'if' => [
            '<?php
if ($foo == true) {
    echo "foo";
}',
            '<?php
if ($foo == true) {

    echo "foo";

}',
        ];

        yield 'else' => [
            '<?php
if ($foo == true) {
    echo "foo";
} else {
    echo "bar";
}',
            '<?php
if ($foo == true) {
    echo "foo";
} else {

    echo "bar";

}',
        ];

        yield 'elseif' => [
            '<?php
if ($foo == true) {
    echo "foo";
} elseif ($bar == true) {
    echo "bar";
}',
            '<?php
if ($foo == true) {
    echo "foo";
} elseif ($bar == true) {

    echo "bar";

}',
        ];

        yield 'else if' => [
            '<?php
if ($foo == true) {
    echo "foo";
} else if ($bar == true) {
    echo "bar";
}',
            '<?php
if ($foo == true) {
    echo "foo";
} else if ($bar == true) {

    echo "bar";

}',
        ];

        yield 'for' => [
            '<?php
for (;;) {
    echo "foo";
}',
            '<?php
for (;;) {

    echo "foo";

}',
        ];

        yield 'foreach' => [
            '<?php
foreach ($foo as $bar) {
    echo "foo";
}',
            '<?php
foreach ($foo as $bar) {

    echo "foo";

}',
        ];

        yield 'while' => [
            '<?php
while (true) {
    echo "foo";
}',
            '<?php
while (true) {

    echo "foo";

}',
        ];

        yield 'do while' => [
            '<?php
do {
    echo "foo";
} while (true);',
            '<?php
do {

    echo "foo";

} while (true);',
        ];

        yield 'switch' => [
            '<?php
switch ($foo) {
    case 1:
        echo "foo";
}',
            '<?php
switch ($foo) {

    case 1:
        echo "foo";

}',
        ];

        yield 'try catch finally' => [
            '<?php
try {
    echo "foo";
} catch (Throwable $exception) {
    echo "bar";
} finally {
    echo "baz";
}',
            '<?php
try {

    echo "foo";

} catch (Throwable $exception) {

    echo "bar";

} finally {

    echo "baz";

}',
        ];
    }

    /**
     * @dataProvider provideFixPhp70Cases
     * @requires PHP 7.0
     */
    public function testFixPhp70(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPhp70Cases(): iterable
    {
        yield 'anonymous class' => [
            '<?php
$foo = new class() {
    public function foo() {
    }
};',
            '<?php
$foo = new class() {

    public function foo() {
    }

};',
        ];
    }
}
