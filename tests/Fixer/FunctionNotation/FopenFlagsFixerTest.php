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
 * @covers \PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer
 */
final class FopenFlagsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            'missing "b"' => [
                '<?php
                    $a = fopen($foo, \'rw+b\');
                ',
                '<?php
                    $a = fopen($foo, \'rw+\');
                ',
            ],
            'has "t" and "b"' => [
                '<?php
                    $a = \fopen($foo, "rw+b");
                ',
                '<?php
                    $a = \fopen($foo, "rw+bt");
                ',
            ],
            'has "t" and no "b" and binary string mod' => [
                '<?php
                    $a = fopen($foo, b\'rw+b\');
                ',
                '<?php
                    $a = fopen($foo, b\'trw+\');
                ',
            ],
            // configure remove b
            'missing "b" but not configured' => [
                '<?php
                    $a = fopen($foo, \'rw+\');
                ',
                '<?php
                    $a = fopen($foo, \'rw+t\');
                ',
                ['b_mode' => false],
            ],
            '"t" and superfluous "b"' => [
                '<?php
                    $a = fopen($foo, \'r+\');
                    $a = fopen($foo, \'w+r\');
                    $a = fopen($foo, \'r+\');
                    $a = fopen($foo, \'w+r\');
                ',
                '<?php
                    $a = fopen($foo, \'r+bt\');
                    $a = fopen($foo, \'btw+r\');
                    $a = fopen($foo, \'r+tb\');
                    $a = fopen($foo, \'tbw+r\');
                ',
                ['b_mode' => false],
            ],
            'superfluous "b"' => [
                '<?php
                    $a = fopen($foo, \'r+\');
                    $a = fopen($foo, \'w+r\');
                ',
                '<?php
                    $a = fopen($foo, \'r+b\');
                    $a = fopen($foo, \'bw+r\');
                ',
                ['b_mode' => false],
            ],
        ];
    }

    /**
     * @dataProvider provideDoNotFixCases
     */
    public function testDoNotFix(string $expected): void
    {
        $this->doTest($expected);
        $this->fixer->configure(['b_mode' => false]);
        $this->doTest($expected);
    }

    public static function provideDoNotFixCases(): array
    {
        return [
            'not simple flags' => [
                '<?php $a = fopen($foo, "t".$a);',
            ],
            'wrong # of arguments' => [
                '<?php
                    $b = fopen("br+");
                    $c = fopen($foo, "w+", 1, 2 , 3);
                ',
            ],
            '"flags" is too long (must be overridden)' => [
                '<?php $d = fopen($foo, "r+w+a+x+c+etXY");',
            ],
            '"flags" is too short (must be overridden)' => [
                '<?php $d = fopen($foo, "");',
            ],
            'static method call' => [
                '<?php $e = A::fopen($foo, "w+");',
            ],
            'method call' => [
                '<?php $f = $b->fopen($foo, "r+");',
            ],
            'comments, PHPDoc and literal' => [
                '<?php
                    // fopen($foo, "rw");
                    /* fopen($foo, "rw"); */
                    echo("fopen($foo, \"rw\")");
                ',
            ],
            'invalid flag values' => [
                '<?php
                $a = fopen($foo, \'\');
                $a = fopen($foo, \'k\');
                $a = fopen($foo, \'kz\');
                $a = fopen($foo, \'k+\');
                $a = fopen($foo, \'+k\');
                $a = fopen($foo, \'xct++\');
                $a = fopen($foo, \'w+r+r+\');
                $a = fopen($foo, \'+btrw+\');
                $a = fopen($foo, \'b+rw\');
                $a = fopen($foo, \'bbrw+\');
                $a = fopen($foo, \'brw++\');
                $a = fopen($foo, \'++brw\');
                $a = fopen($foo, \'ybrw+\');
                $a = fopen($foo, \'rr\');
                $a = fopen($foo, \'ロ\');
                $a = fopen($foo, \'ロ+\');
                $a = fopen($foo, \'rロ\');
                $a = \fopen($foo, \'w+ロ\');
                ',
            ],
            [
                '<?php
                    echo "abc"; // to pass the candidate check
                    $a = fopen($foo, 1);
                    $a = fopen($foo, $a);
                    $a = fopen($foo, null);
                ',
            ],
        ];
    }
}
