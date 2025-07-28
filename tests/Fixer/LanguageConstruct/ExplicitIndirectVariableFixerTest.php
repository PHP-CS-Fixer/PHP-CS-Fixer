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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer>
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class ExplicitIndirectVariableFixerTest extends AbstractFixerTestCase
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
        yield 'variable variable function call' => [
            '<?php echo ${$foo}($bar);',
            '<?php echo $$foo($bar);',
        ];

        yield 'variable variable array fetch' => [
            '<?php echo ${$foo}[\'bar\'][\'baz\'];',
            '<?php echo $$foo[\'bar\'][\'baz\'];',
        ];

        yield 'dynamic property access' => [
            '<?php echo $foo->{$bar}[\'baz\'];',
            '<?php echo $foo->$bar[\'baz\'];',
        ];

        yield 'dynamic property access with method call' => [
            '<?php echo $foo->{$bar}[\'baz\']();',
            '<?php echo $foo->$bar[\'baz\']();',
        ];

        yield 'variable variable with comments between dollar signs' => [
            '<?php echo $
/* C1 */
// C2
{$foo}
// C3
;',
            '<?php echo $
/* C1 */
// C2
$foo
// C3
;',
        ];

        yield 'dynamic static property access using variable variable' => [
            '<?php echo Foo::${$bar};',
            '<?php echo Foo::$$bar;',
        ];

        yield 'dynamic static property access using variable variable with method call' => [
            '<?php echo Foo::${$bar}->baz();',
            '<?php echo Foo::$$bar->baz();',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'dynamic property fetch with nullsafe operator' => [
            '<?php echo $foo?->{$bar}["baz"];',
            '<?php echo $foo?->$bar["baz"];',
        ];

        yield 'dynamic property fetch with nullsafe operator and method call' => [
            '<?php echo $foo?->{$bar}["baz"]();',
            '<?php echo $foo?->$bar["baz"]();',
        ];
    }

    /**
     * @dataProvider provideFix83Cases
     *
     * @requires PHP 8.3
     */
    public function testFix83(string $expected, ?string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFix83Cases(): iterable
    {
        yield 'dynamic class const fetch with variable variable' => [
            '<?php echo Foo::{${$bar}};',
            '<?php echo Foo::{$$bar};',
        ];
    }
}
