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
 * @author Gregor Harlan
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\CombineNestedDirnameFixer
 */
final class CombineNestedDirnameFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     * @requires PHP 7.0
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php dirname();',
            ],
            [
                '<?php dirname($path);',
            ],
            [
                '<?php dirname($path, 3);',
            ],
            [
                '<?php dirname($path,2);',
                '<?php dirname(dirname($path));',
            ],
            [
                '<?php dirname /* a */ ( /* b */  /* c */ $path /* d */,2);',
                '<?php dirname /* a */ ( /* b */ dirname( /* c */ $path) /* d */);',
            ],
            [
                '<?php dirname($path,3);',
                '<?php dirname(\dirname(dirname($path)));',
            ],
            [
                '<?php dirname($path ,4);',
                '<?php dirname(dirname($path, 3));',
            ],
            [
                '<?php dirname($path, 4);',
                '<?php dirname(dirname($path), 3);',
            ],
            [
                '<?php dirname($path , 5);',
                '<?php dirname(dirname($path, 2), 3);',
            ],
            [
                '<?php dirname($path ,5);',
                '<?php dirname(dirname(dirname($path), 3));',
            ],
            [
                '<?php dirname(dirname($path, $level));',
            ],
            [
                '<?php dirname("foo/".dirname($path));',
            ],
            [
                '<?php dirname(dirname($path).$foo);',
            ],
            [
                '<?php foo\dirname(dirname($path));',
            ],
            [
                '<?php dirname(foo(dirname($path,2)),2);',
                '<?php dirname(dirname(foo(dirname(dirname($path)))));',
            ],
            [
                '<?php new dirname(dirname($path,2));',
                '<?php new dirname(dirname(dirname($path)));',
            ],
        ];
    }

    /**
     * @requires PHP <7.0
     */
    public function testDoNotFix()
    {
        $this->doTest('<?php dirname(dirname($path));');
    }
}
