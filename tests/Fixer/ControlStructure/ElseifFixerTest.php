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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\ElseifFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ControlStructure\ElseifFixer>
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
final class ElseifFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php if ($some) { $test = true; } else { $test = false; }'];

        yield [
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            '<?php if ($some) { $test = true; } else if ($some !== "test") { $test = false; }',
        ];

        yield [
            '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
            '<?php if ($some) { $test = true; } else  if ($some !== "test") { $test = false; }',
        ];

        yield [
            '<?php $js = \'if (foo.a) { foo.a = "OK"; } else if (foo.b) { foo.b = "OK"; }\';',
        ];

        yield [
            '<?php
                    if ($a) {
                        $x = 1;
                    } elseif ($b) {
                        $x = 2;
                    }',
            '<?php
                    if ($a) {
                        $x = 1;
                    } else
                    if ($b) {
                        $x = 2;
                    }',
        ];

        yield [
            '<?php
                    if ($a) {
                    } elseif/**/ ($b) {
                    }
                ',
            '<?php
                    if ($a) {
                    } else /**/ if ($b) {
                    }
                ',
        ];

        yield [
            '<?php
                    if ($a) {
                    } elseif//
                        ($b) {
                    }
                ',
            '<?php
                    if ($a) {
                    } else //
                        if ($b) {
                    }
                ',
        ];

        yield [
            '<?php if ($a) {} /**/elseif ($b){}',
            '<?php if ($a) {} /**/else if ($b){}',
        ];

        yield ['<?php if ($x) { foo(); } else if ($y): bar(); endif;'];
    }
}
