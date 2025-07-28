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

namespace PhpCsFixer\Tests\Fixer\ReturnNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer>
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class SimplifiedNullReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: non-empty-string, 1?: non-empty-string}>
     */
    public static function provideFixCases(): iterable
    {
        // check correct statements aren't changed
        yield ['<?php return  ;'];

        yield ['<?php return \'null\';'];

        yield ['<?php return false;'];

        yield ['<?php return (false );'];

        yield ['<?php return null === foo();'];

        yield ['<?php return array() == null ;'];

        // check we modified those that can be changed
        yield ['<?php return;', '<?php return null;'];

        yield ['<?php return;', '<?php return (null);'];

        yield ['<?php return;', '<?php return ( null    );'];

        yield ['<?php return;', '<?php return ( (( null)));'];

        yield ['<?php return /* hello */;', '<?php return /* hello */ null  ;'];

        yield ['<?php return;', '<?php return NULL;'];

        yield ['<?php return;', "<?php return\n(\nnull\n)\n;"];

        yield ['<?php function foo(): ? /* C */ int { return null; }'];

        yield ['<?php function foo(): ?int { if (false) { return null; } }'];

        yield ['<?php function foo(): int { return null; }'];

        yield ['<?php function foo(): A\B\C { return null; }'];

        yield [
            '<?php function foo(): ?int { return null; } return;',
            '<?php function foo(): ?int { return null; } return null;',
        ];

        yield [
            '<?php function foo() { return; } function bar(): ?A\B\C\D { return null; } function baz() { return; }',
            '<?php function foo() { return null; } function bar(): ?A\B\C\D { return null; } function baz() { return null; }',
        ];

        yield [
            '<?php function foo(): ?int { $bar = function() { return; }; return null; }',
            '<?php function foo(): ?int { $bar = function() { return null; }; return null; }',
        ];

        yield [
            '<?php function foo(): void { return; }',
        ];

        yield ['<?php return ?>', '<?php return null ?>'];

        yield ['<?php return [] ?>'];

        yield [
            '<?php
                    return // hello
                    ?>
                ',
            '<?php
                    return null // hello
                    ?>
                ',
        ];

        yield [
            '<?php
                    return
                    // hello
                    ?>
                ',
            '<?php
                    return null
                    // hello
                    ?>
                ',
        ];

        yield [
            '<?php
                    return // hello
                    ;
                ',
            '<?php
                    return null // hello
                    ;
                ',
        ];

        yield [
            '<?php
                    return
                    // hello
                    ;
                ',
            '<?php
                    return null
                    // hello
                    ;
                ',
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: non-empty-string, 1?: non-empty-string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php
            function test(): null|int
            {
                if (true) { return null; }
                return 42;
            }',
        ];

        yield [
            '<?php
            function test(): null|array
            {
                if (true) { return null; }
                return [];
            }',
        ];

        yield [
            '<?php
            function test(): array|null
            {
                if (true) { return null; }
                return [];
            }',
        ];
    }
}
