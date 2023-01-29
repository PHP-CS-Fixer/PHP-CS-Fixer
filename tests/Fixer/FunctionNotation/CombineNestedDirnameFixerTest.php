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
 * @author Gregor Harlan
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\CombineNestedDirnameFixer
 */
final class CombineNestedDirnameFixerTest extends AbstractFixerTestCase
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
                '<?php dirname($path, 2);',
                '<?php dirname(dirname($path));',
            ],
            [
                '<?php dirname /* a */ ( /* b */ /* c */ $path /* d */, 2);',
                '<?php dirname /* a */ ( /* b */ dirname( /* c */ $path) /* d */);',
            ],
            [
                '<?php dirname($path, 3);',
                '<?php dirname(\dirname(dirname($path)));',
            ],
            [
                '<?php dirname($path, 4);',
                '<?php dirname(dirname($path, 3));',
            ],
            [
                '<?php dirname($path, 4);',
                '<?php dirname(dirname($path), 3);',
            ],
            [
                '<?php dirname($path, 5);',
                '<?php dirname(dirname($path, 2), 3);',
            ],
            [
                '<?php dirname($path, 5);',
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
                '<?php dirname(foo(dirname($path, 2)), 2);',
                '<?php dirname(dirname(foo(dirname(dirname($path)))));',
            ],
            [
                '<?php new dirname(dirname($path, 2));',
                '<?php new dirname(dirname(dirname($path)));',
            ],
            [
                '<?php dirname($path, 3);',
                '<?php dirname(dirname(dirname($path, ), ));',
            ],
            [
                '<?php dirname($path, 3);',
                '<?php dirname(dirname(dirname($path, ), ), );',
            ],
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield ['<?php $a = dirname(dirname(...));'];
    }
}
