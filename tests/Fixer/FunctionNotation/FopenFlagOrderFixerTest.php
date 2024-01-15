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
            <<<'EOD'
                <?php
                                    $a = fopen($foo, 'rw+b');
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = fopen($foo, 'brw+');
                EOD."\n                ",
        ];

        yield '"fopen" casing insensitive' => [
            <<<'EOD'
                <?php
                                    $a = \FOPen($foo, "cr+w+b");
                                    $a = \FOPEN($foo, "crw+b");
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = \FOPen($foo, "bw+r+c");
                                    $a = \FOPEN($foo, "bw+rc");
                EOD."\n                ",
        ];

        yield 'comments around flags' => [
            <<<'EOD'
                <?php
                                    $a = fopen($foo,/*0*/'rb'/*1*/);
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = fopen($foo,/*0*/'br'/*1*/);
                EOD."\n                ",
        ];

        yield 'binary string' => [
            <<<'EOD'
                <?php
                                    $a = \fopen($foo, b"cr+w+b");
                                    $b = \fopen($foo, B"crw+b");
                                    $c = \fopen($foo, b'cr+w+b');
                                    $d = \fopen($foo, B'crw+b');
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = \fopen($foo, b"bw+r+c");
                                    $b = \fopen($foo, B"bw+rc");
                                    $c = \fopen($foo, b'bw+r+c');
                                    $d = \fopen($foo, B'bw+rc');
                EOD."\n                ",
        ];

        yield 'common typos' => [
            <<<'EOD'
                <?php
                                     $a = fopen($a, "b+r");
                                     $b = fopen($b, 'b+w');
                EOD."\n                ",
        ];

        // `t` cases
        yield [
            <<<'EOD'
                <?php
                                    $a = fopen($foo, 'rw+t');
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = fopen($foo, 'trw+');
                EOD."\n                ",
        ];

        yield [
            <<<'EOD'
                <?php
                                    $a = \fopen($foo, 'rw+tb');
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    $a = \fopen($foo, 'btrw+');
                EOD."\n                ",
        ];

        // don't fix cases
        yield 'single flag' => [
            <<<'EOD'
                <?php
                                    $a = fopen($foo, "r");
                                    $a = fopen($foo, "r+");
                EOD."\n                ",
        ];

        yield 'not simple flags' => [
            <<<'EOD'
                <?php
                                    $a = fopen($foo, "br+".$a);
                EOD."\n                ",
        ];

        yield 'wrong # of arguments' => [
            <<<'EOD'
                <?php
                                    $b = \fopen("br+");
                                    $c = fopen($foo, "bw+", 1, 2 , 3);
                EOD."\n                ",
        ];

        yield '"flags" is too long (must be overridden)' => [
            <<<'EOD'
                <?php
                                    $d = fopen($foo, "r+w+a+x+c+etbX");
                EOD."\n                ",
        ];

        yield 'static method call' => [
            <<<'EOD'
                <?php
                                    $e = A::fopen($foo, "bw+");
                EOD."\n                ",
        ];

        yield 'method call' => [
            <<<'EOD'
                <?php
                                    $f = $b->fopen($foo, "br+");
                EOD."\n                ",
        ];

        yield 'comments, PHPDoc and literal' => [
            <<<'EOD'
                <?php
                                    // fopen($foo, "brw");
                                    /* fopen($foo, "brw"); */
                                    echo("fopen($foo, \"brw\")");
                EOD."\n                ",
        ];

        yield 'invalid flag values' => [
            <<<'EOD'
                <?php
                                    $a = fopen($foo, '');
                                    $a = fopen($foo, 'x'); // ok but should not mark collection as changed
                                    $a = fopen($foo, 'k');
                                    $a = fopen($foo, 'kz');
                                    $a = fopen($foo, 'k+');
                                    $a = fopen($foo, '+k');
                                    $a = fopen($foo, 'xc++');
                                    $a = fopen($foo, 'w+r+r+');
                                    $a = fopen($foo, '+brw+');
                                    $a = fopen($foo, 'b+rw');
                                    $a = fopen($foo, 'bbrw+');
                                    $a = fopen($foo, 'brw++');
                                    $a = fopen($foo, '++brw');
                                    $a = fopen($foo, 'ybrw+');
                                    $a = fopen($foo, 'rr');
                                    $a = fopen($foo, 'ロ');
                                    $a = fopen($foo, 'ロ+');
                                    $a = \fopen($foo, 'rロ');
                                    $a = \fopen($foo, 'w+ロ');
                EOD."\n                ",
        ];
    }
}
