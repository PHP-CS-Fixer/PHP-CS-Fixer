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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\EregToPregFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Alias\EregToPregFixer>
 *
 * @author Matteo Beccati <matteo@beccati.com>
 */
final class EregToPregFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php $x = 1;'];

        yield ['<?php $x = "ereg";'];

        yield ['<?php $x = ereg("[A-Z]"."foo", $m);'];

        yield ['<?php $x = ereg("^*broken", $m);'];

        yield ['<?php $x = Foo::split("[A-Z]", $m);'];

        yield ['<?php $x = $foo->split("[A-Z]", $m);'];

        yield ['<?php $x = Foo\split("[A-Z]", $m);'];

        yield [
            '<?php $x = preg_match(\'/[A-Z]/D\');',
            '<?php $x = ereg(\'[A-Z]\');',
        ];

        yield [
            '<?php $x = preg_match(\'/[A-Z]/D\', $m);',
            '<?php $x = ereg(\'[A-Z]\', $m);',
        ];

        yield [
            '<?php $x = preg_match("/[A-Z]/D", $m);',
            '<?php $x = ereg("[A-Z]", $m);',
        ];

        yield [
            '<?php $x = preg_match("/[A-Z]/Di", $m);',
            '<?php $x = eregi("[A-Z]", $m);',
        ];

        yield [
            '<?php $x = preg_match("#/[AZ]#D", $m);',
            '<?php $x = ereg("/[AZ]", $m);',
        ];

        yield [
            '<?php $x = preg_match("#[AZ]/#D", $m);',
            '<?php $x = ereg("[AZ]/", $m);',
        ];

        yield [
            '<?php $x = preg_match("!#[A]/!D", $m);',
            '<?php $x = ereg("#[A]/", $m);',
        ];

        yield [
            '<?php $x = preg_match("!##[A\!]//!D", $m);',
            '<?php $x = ereg("##[A!]//", $m);',
        ];

        yield [
            '<?php $x = preg_match("/##[A!!]\/\//D", $m);',
            '<?php $x = ereg("##[A!!]//", $m);',
        ];

        yield [
            '<?php $x = preg_match("#\#\#[A!!]///#D", $m);',
            '<?php $x = ereg("##[A!!]///", $m);',
        ];

        yield [
            '<?php $x = preg_replace("/[A-Z]/D", "", $m);',
            '<?php $x = ereg_replace("[A-Z]", "", $m);',
        ];

        yield [
            '<?php $x = preg_replace("/[A-Z]/Di", "", $m);',
            '<?php $x = eregi_replace("[A-Z]", "", $m);',
        ];

        yield [
            '<?php $x = preg_split("/[A-Z]/D", $m);',
            '<?php $x = split("[A-Z]", $m);',
        ];

        yield [
            '<?php $x = preg_split("/[A-Z]/Di", $m);',
            '<?php $x = spliti("[A-Z]", $m);',
        ];

        yield 'binary lowercase' => [
            '<?php $x = preg_split(b"/[A-Z]/Di", $m);',
            '<?php $x = spliti(b"[A-Z]", $m);',
        ];

        yield 'binary uppercase' => [
            '<?php $x = preg_split(B"/[A-Z]/Di", $m);',
            '<?php $x = spliti(B"[A-Z]", $m);',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php $x = spliti(...);',
        ];
    }
}
