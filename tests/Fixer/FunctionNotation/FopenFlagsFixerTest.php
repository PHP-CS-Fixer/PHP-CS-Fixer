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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer
 */
final class FopenFlagsFixerTest extends AbstractFixerTestCase
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
                    $a = fopen($foo, "rw+b");
                ',
                '<?php
                    $a = fopen($foo, "rw+bt");
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
            // don't fix cases
            'not simple flags' => [
                '<?php
                    $a = fopen($foo, "t".$a);
                ',
            ],
            'wrong # of arguments' => [
                '<?php
                    $b = fopen("br+");
                    $c = fopen($foo, "w+", 1, 2 , 3);
                ',
            ],
            '"flags" is too long (must be overridden)' => [
                '<?php
                    $d = fopen($foo, "r+w+a+x+c+etXY");
                ',
            ],
            '"flags" is too short (must be overridden)' => [
                '<?php
                    $d = fopen($foo, "");
                ',
            ],
            'static method call' => [
                '<?php
                    $e = A::fopen($foo, "w+");
                ',
            ],
            'method call' => [
                '<?php
                    $f = $b->fopen($foo, "r+");
                ',
            ],
            'comments, PHPDoc and literal' => [
                '<?php
                    // fopen($foo, "rw");
                    /* fopen($foo, "rw"); */
                    echo("fopen($foo, \"rw\")");
                ',
            ],
        ];
    }
}
