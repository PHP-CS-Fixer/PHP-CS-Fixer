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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Timothée Garnaud <tgarnaud@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\NotOperatorToFalseFixer
 */
final class NotOperatorToFalseFixerTest extends AbstractFixerTestCase
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
            '<?php if (!strpos("abcd", "a"));',
            '<?php if (false == strpos("abcd", "a"))',
        ];

        yield [
            '<?php $a = !$b',
            '<?php $a = false == $b',
        ];

        yield [
            '<?php !($a || $b);',
            '<?php false == ($a || $b)',
        ];
    }
}
