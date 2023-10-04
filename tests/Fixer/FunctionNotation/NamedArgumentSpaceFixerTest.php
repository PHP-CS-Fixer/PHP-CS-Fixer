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
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NamedArgumentSpaceFixer
 *
 * @requires PHP 8.0
 */
final class NamedArgumentSpaceFixerTest extends AbstractFixerTestCase
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
        yield [
            '<?php foo1(a: 1);',
            '<?php foo1(a   : 1);',
        ];

        yield [
            '<?php foo2(a: 1);',
            '<?php foo2(a:1);',
        ];

        yield [
            '<?php foo3(a: 1);',
            '<?php foo3(a:    1);',
        ];

        yield [
            '<?php foo4(a/* X */    : 1);',
            '<?php foo4(a     /* X */    :    1);',
        ];

        yield [
            '<?php foo5(a: 1);',
            '<?php foo5(a:
1);',
        ];

        yield [
            '<?php foo6(a: 1,g: 2,   h: 1,   j: 9);',
            '<?php foo6(a   : 1,g:2,   h  :  1,   j:        9);',
        ];
    }
}
