<?php

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
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer
 */
final class SimplifiedNullReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            // check correct statements aren't changed
            ['<?php return  ;'],
            ['<?php return \'null\';'],
            ['<?php return false;'],
            ['<?php return (false );'],
            ['<?php return null === foo();'],
            ['<?php return array() == null ;'],

            // check we modified those that can be changed
            ['<?php return;', '<?php return null;'],
            ['<?php return;', '<?php return (null);'],
            ['<?php return;', '<?php return ( null    );'],
            ['<?php return;', '<?php return ( (( null)));'],
            ['<?php return /* hello */;', '<?php return /* hello */ null  ;'],
            ['<?php return;', '<?php return NULL;'],
            ['<?php return;', "<?php return\n(\nnull\n)\n;"],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideNullableReturnTypeCases
     * @requires PHP 7.1
     */
    public function test71ReturnTypes($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideNullableReturnTypeCases()
    {
        return [
            ['<?php function foo(): ? /* C */ int { return null; }'],
            ['<?php function foo(): ?int { if (false) { return null; } }'],
            ['<?php function foo(): int { return null; }'],
            ['<?php function foo(): A\B\C { return null; }'],
            [
                '<?php function foo(): ?int { return null; } return;',
                '<?php function foo(): ?int { return null; } return null;',
            ],
            [
                '<?php function foo() { return; } function bar(): ?A\B\C\D { return null; } function baz() { return; }',
                '<?php function foo() { return null; } function bar(): ?A\B\C\D { return null; } function baz() { return null; }',
            ],
            [
                '<?php function foo(): ?int { $bar = function() { return; }; return null; }',
                '<?php function foo(): ?int { $bar = function() { return null; }; return null; }',
            ],
            [
                '<?php function foo(): void { return; }',
                '<?php function foo(): void { return null; }',
            ],
        ];
    }
}
