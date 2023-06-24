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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\FloatLiteralCaseFixer
 */
final class FloatLiteralCaseFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $foo1 = 1.0; $foo2 = 1E+10; $foo3 = -1E-10;',
            '<?php $foo1 = 1.0; $foo2 = 1e+10; $foo3 = -1e-10;',
        ];

        yield [
            '<?php $foo = 1E0;',
            '<?php $foo = 1e0;',
        ];

        yield [
            '<?php $foo = .1E-0;',
            '<?php $foo = .1e-0;',
        ];

        yield [
            '<?php $foo = 8.2023437675747321E320;',
            '<?php $foo = 8.2023437675747321e320;',
        ];

        yield [
            '<?php $foo = 123_456_789E+0_2_0;',
            '<?php $foo = 123_456_789e+0_2_0;',
        ];
    }
}
