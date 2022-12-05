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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer
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

    public static function provideFixCases(): array
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
            ],
        ];
    }
}
