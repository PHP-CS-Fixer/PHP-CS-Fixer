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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\NotOperatorWithSpaceFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\NotOperatorWithSpaceFixer>
 *
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NotOperatorWithSpaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $i = 0; $i++; ++$i; $foo = ! false || ( ! true);',
            '<?php $i = 0; $i++; ++$i; $foo = !false || (!true);',
        ];

        yield [
            '<?php $i = 0; $i--; --$i; $foo = ! false || ($i && ! true);',
            '<?php $i = 0; $i--; --$i; $foo = !false || ($i && !true);',
        ];

        yield [
            '<?php $i = 0; $i--; $foo = ! false || ($i && ! /* some comment */true);',
            '<?php $i = 0; $i--; $foo = !false || ($i && !/* some comment */true);',
        ];

        yield [
            '<?php $i = 0; $i--; $foo = ! false || ($i && !    true);',
            '<?php $i = 0; $i--; $foo = !false || ($i && !    true);',
        ];

        yield [
            '<?php $i = 0; $i--; $foo = ! false || ($i &&    !    true);',
            '<?php $i = 0; $i--; $foo = !false || ($i &&    !    true);',
        ];
    }
}
