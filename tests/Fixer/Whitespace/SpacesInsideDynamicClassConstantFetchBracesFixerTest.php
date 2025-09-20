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
 * @covers \PhpCsFixer\Fixer\Whitespace\SpacesInsideDynamicClassConstantFetchBracesFixer
 *
 * @internal
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Whitespace\SpacesInsideDynamicClassConstantFetchBracesFixer>
 *
 * @requires PHP 8.3
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class SpacesInsideDynamicClassConstantFetchBracesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'basic variable' => [
            '<?php echo Foo::{$bar};',
            '<?php echo Foo::{  $bar  };',
        ];

        yield 'function call' => [
            '<?php echo Foo::{bar()};',
            '<?php echo Foo::{  bar()  };',
        ];

        yield 'array access' => [
            '<?php echo Foo::{$baz[\'bar\'][\'baz\']};',
            '<?php echo Foo::{  $baz[\'bar\'][\'baz\']  };',
        ];

        yield 'mixed spaces' => [
            '<?php echo Foo::{$bar} . Bar::{$baz};',
            '<?php echo Foo::{ $bar } . Bar::{   $baz   };',
        ];

        yield 'no spaces needed' => [
            '<?php echo Foo::{$bar};',
        ];

        yield 'empty braces should not be touched' => [
            '<?php echo Foo::{};',
        ];

        yield 'nested example' => [
            '<?php echo Foo::{$baz[Bar::{$other}]};',
            '<?php echo Foo::{  $baz[Bar::{  $other  }]  };',
        ];

        yield 'with comments' => [
            '<?php echo Foo::{/* comment */ $bar /* comment */};',
            '<?php echo Foo::{  /* comment */ $bar /* comment */  };',
        ];
    }

    /**
     * @dataProvider provideFixWithConfigCases
     */
    public function testFixWithConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['space' => 'single']);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixWithConfigCases(): iterable
    {
        yield 'basic variable single space' => [
            '<?php echo Foo::{ $bar };',
            '<?php echo Foo::{$bar};',
        ];

        yield 'function call single space' => [
            '<?php echo Foo::{ bar() };',
            '<?php echo Foo::{bar()};',
        ];

        yield 'remove multiple spaces' => [
            '<?php echo Foo::{ $bar };',
            '<?php echo Foo::{  $bar  };',
        ];

        yield 'already correct single space' => [
            '<?php echo Foo::{ $bar };',
        ];

        yield 'empty braces single space should not add spaces' => [
            '<?php echo Foo::{};',
        ];
    }
}