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
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\ElseifFixer
 */
final class ElseifFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideTestFixCases(): array
    {
        return [
            ['<?php if ($some) { $test = true; } else { $test = false; }'],
            [
                '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
                '<?php if ($some) { $test = true; } else if ($some !== "test") { $test = false; }',
            ],
            [
                '<?php if ($some) { $test = true; } elseif ($some !== "test") { $test = false; }',
                '<?php if ($some) { $test = true; } else  if ($some !== "test") { $test = false; }',
            ],
            [
                '<?php $js = \'if (foo.a) { foo.a = "OK"; } else if (foo.b) { foo.b = "OK"; }\';',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
                '<?php if ($a) {} /**/elseif ($b){}',
                '<?php if ($a) {} /**/else if ($b){}',
            ],
            ['<?php if ($x) { foo(); } else if ($y): bar(); endif;'],
        ];
    }
}
