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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFopenFlagFixer
 * @covers \PhpCsFixer\Fixer\FunctionNotation\FopenFlagOrderFixer
 */
final class FopenFlagOrderFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'most simple fix case' => [
            '<?php
                    $a = fopen($foo, \'rw+b\');
                ',
            '<?php
                    $a = fopen($foo, \'brw+\');
                ',
        ];

        yield '"fopen" casing insensitive' => [
            '<?php
                    $a = \FOPen($foo, "cr+w+b");
                    $a = \FOPEN($foo, "crw+b");
                ',
            '<?php
                    $a = \FOPen($foo, "bw+r+c");
                    $a = \FOPEN($foo, "bw+rc");
                ',
        ];

        yield 'comments around flags' => [
            '<?php
                    $a = fopen($foo,/*0*/\'rb\'/*1*/);
                ',
            '<?php
                    $a = fopen($foo,/*0*/\'br\'/*1*/);
                ',
        ];

        yield 'binary string' => [
            '<?php
                    $a = \fopen($foo, b"cr+w+b");
                    $b = \fopen($foo, B"crw+b");
                    $c = \fopen($foo, b\'cr+w+b\');
                    $d = \fopen($foo, B\'crw+b\');
                ',
            '<?php
                    $a = \fopen($foo, b"bw+r+c");
                    $b = \fopen($foo, B"bw+rc");
                    $c = \fopen($foo, b\'bw+r+c\');
                    $d = \fopen($foo, B\'bw+rc\');
                ',
        ];

        yield 'common typos' => [
            '<?php
                     $a = fopen($a, "b+r");
                     $b = fopen($b, \'b+w\');
                ',
        ];

        // `t` cases
        yield [
            '<?php
                    $a = fopen($foo, \'rw+t\');
                ',
            '<?php
                    $a = fopen($foo, \'trw+\');
                ',
        ];

        yield [
            '<?php
                    $a = \fopen($foo, \'rw+tb\');
                ',
            '<?php
                    $a = \fopen($foo, \'btrw+\');
                ',
        ];

        // don't fix cases
        yield 'single flag' => [
            '<?php
                    $a = fopen($foo, "r");
                    $a = fopen($foo, "r+");
                ',
        ];

        yield 'not simple flags' => [
            '<?php
                    $a = fopen($foo, "br+".$a);
                ',
        ];

        yield 'wrong # of arguments' => [
            '<?php
                    $b = \fopen("br+");
                    $c = fopen($foo, "bw+", 1, 2 , 3);
                ',
        ];

        yield '"flags" is too long (must be overridden)' => [
            '<?php
                    $d = fopen($foo, "r+w+a+x+c+etbX");
                ',
        ];

        yield 'static method call' => [
            '<?php
                    $e = A::fopen($foo, "bw+");
                ',
        ];

        yield 'method call' => [
            '<?php
                    $f = $b->fopen($foo, "br+");
                ',
        ];

        yield 'comments, PHPDoc and literal' => [
            '<?php
                    // fopen($foo, "brw");
                    /* fopen($foo, "brw"); */
                    echo("fopen($foo, \"brw\")");
                ',
        ];

        yield 'invalid flag values' => [
            '<?php
                    $a = fopen($foo, \'\');
                    $a = fopen($foo, \'x\'); // ok but should not mark collection as changed
                    $a = fopen($foo, \'k\');
                    $a = fopen($foo, \'kz\');
                    $a = fopen($foo, \'k+\');
                    $a = fopen($foo, \'+k\');
                    $a = fopen($foo, \'xc++\');
                    $a = fopen($foo, \'w+r+r+\');
                    $a = fopen($foo, \'+brw+\');
                    $a = fopen($foo, \'b+rw\');
                    $a = fopen($foo, \'bbrw+\');
                    $a = fopen($foo, \'brw++\');
                    $a = fopen($foo, \'++brw\');
                    $a = fopen($foo, \'ybrw+\');
                    $a = fopen($foo, \'rr\');
                    $a = fopen($foo, \'ロ\');
                    $a = fopen($foo, \'ロ+\');
                    $a = \fopen($foo, \'rロ\');
                    $a = \fopen($foo, \'w+ロ\');
                ',
        ];
    }
}
