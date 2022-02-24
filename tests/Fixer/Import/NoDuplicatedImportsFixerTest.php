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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\NoDuplicatedImportsFixer
 */
final class NoDuplicatedImportsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->linter = new Linter();
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield 'same import but in different namespace' => [
            '<?php
namespace {
    use Bar;

    echo Bar::foo;
}

namespace A {
    use Bar;

    echo Bar::foo;
}
            ',
        ];

        yield 'multiple duplicates' => [
            '<?php use A;      ',
            '<?php use A;use A;use A;use A;use A;use A;use A;',
        ];

        yield 'no leading - no leading' => [
            '<?php
use Throwable1;
 ',
            '<?php
use Throwable1;
use Throwable1;',
        ];

        yield 'leading - leading' => [
            '<?php
use \Throwable2;
 ',
            '<?php
use \Throwable2;
use \Throwable2;',
        ];

        yield 'leading - no leading' => [
            '<?php
use \Throwable3;
 ',
            '<?php
use \Throwable3;
use Throwable3;',
        ];

        yield 'no leading - leading' => [
            '<?php
use Throwable4;
 ',
            '<?php
use Throwable4;
use \Throwable4;',
        ];

        yield 'do not remove close tag' => [
            '<?php
use Throwable5;
  ?> Foo
',
            '<?php
use Throwable5;
use Throwable5 ?> Foo
',
        ];

        yield 'comments are preserved' => [
            '<?php use A;/* A *//* B *//* C *//* D */ // foo',
            '<?php use A;/* A */use/* B */A/* C */;/* D */ // foo',
        ];

        yield 'function' => [
            '<?php use function A;   ',
            '<?php use function A; use function A;',
        ];

        yield 'const' => [
            '<?php use const A;   ',
            '<?php use const A; use const A;',
        ];

        yield 'scattered' => [
            '<?php declare(strict_types=1);namespace B; use const A; echo 123; $a = time(); if ($a % 2 == 1) { echo "foo"; };   ?>X<?php echo 4567;',
            '<?php declare(strict_types=1);namespace B; use const A; echo 123; $a = time(); if ($a % 2 == 1) { echo "foo"; }; use const A?>X<?php echo 4567;',
        ];

        yield 'mixed import types |' => [
            '<?php
             use A;
             use const A;',
        ];

        yield 'mixed import types ||' => [
            '<?php
             use const A;
             use function A;',
        ];

        yield 'mixed import types |||' => [
            '<?php
             use A;
             use const A;
             use function A;',
        ];

        yield 'whatever' => [
            '<?php
namespace FooBar;
use Vendor\Project\Duplicated\Foo;
use Vendor\Foo;
use Vendor\Project\Duplicated\Bar;
 '.'
 '.'
use Vendor\Bar;
 '.'
 '.'
',
            '<?php
namespace FooBar;
use Vendor\Project\Duplicated\Foo;
use Vendor\Foo;
use Vendor\Project\Duplicated\Bar;
use Vendor\Project\Duplicated\Foo;
use Vendor\Project\Duplicated\Bar;
use Vendor\Bar;
use Vendor\Project\Duplicated\Foo;
use Vendor\Project\Duplicated\Foo;
',
        ];
    }
}
