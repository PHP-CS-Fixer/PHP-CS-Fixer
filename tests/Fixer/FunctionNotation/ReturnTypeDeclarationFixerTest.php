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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer
 */
final class ReturnTypeDeclarationFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '#^\[return_type_declaration\] Invalid configuration: The option "s" does not exist\. (Known|Defined) options are: "space_before"\.$#'
        );

        $this->fixer->configure(['s' => 9_000]);
    }

    /**
     * @dataProvider provideFixWithSpaceBeforeNoneCases
     */
    public function testFixWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixWithSpaceBeforeNoneCases
     */
    public function testFixWithSpaceBeforeNone(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'space_before' => 'none',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithSpaceBeforeNoneCases(): iterable
    {
        yield [
            '<?php function foo1(int $a) {}',
        ];

        yield [
            '<?php function foo2(int $a): string {}',
            '<?php function foo2(int $a):string {}',
        ];

        yield [
            '<?php function foo3(int $c)/**/ : /**/ string {}',
        ];

        yield [
            '<?php function foo4(int $a): string {}',
            '<?php function foo4(int $a)  :  string {}',
        ];

        yield [
            '<?php function foo5(int $e)#
: #
#
string {}',
            '<?php function foo5(int $e)#
:#
#
string {}',
        ];

        yield [
            '<?php
                    function foo1(int $a): string {}
                    function foo2(int $a): string {}
                    function foo3(int $a): string {}
                    function foo4(int $a): string {}
                    function foo5(int $a): string {}
                    function foo6(int $a): string {}
                    function foo7(int $a): string {}
                    function foo8(int $a): string {}
                    function foo9(int $a): string {}
                ',
            '<?php
                    function foo1(int $a):string {}
                    function foo2(int $a):string {}
                    function foo3(int $a):string {}
                    function foo4(int $a):string {}
                    function foo5(int $a):string {}
                    function foo6(int $a):string {}
                    function foo7(int $a):string {}
                    function foo8(int $a):string {}
                    function foo9(int $a):string {}
                ',
        ];

        yield [
            '<?php fn(): int => 1;',
            '<?php fn():int => 1;',
        ];
    }

    /**
     * @dataProvider provideFixWithSpaceBeforeOneCases
     */
    public function testFixWithSpaceBeforeOne(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'space_before' => 'one',
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithSpaceBeforeOneCases(): iterable
    {
        yield [
            '<?php function fooA(int $a) {}',
        ];

        yield [
            '<?php function fooB(int $a) : string {}',
            '<?php function fooB(int $a):string {}',
        ];

        yield [
            '<?php function fooC(int $a)/**/ : /**/string {}',
            '<?php function fooC(int $a)/**/:/**/string {}',
        ];

        yield [
            '<?php function fooD(int $a) : string {}',
            '<?php function fooD(int $a)  :  string {}',
        ];

        yield [
            '<?php function fooE(int $a) /**/ : /**/ string {}',
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
        yield [
            '<?php function foo(): mixed{}',
            '<?php function foo()   :   mixed{}',
        ];

        yield [
            '<?php class A { public function foo(): static{}}',
            '<?php class A { public function foo()   :static{}}',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php enum Foo: int {}',
            '<?php enum Foo   :   int {}',
        ];
    }
}
