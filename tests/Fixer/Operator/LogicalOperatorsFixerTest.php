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
 * @covers \PhpCsFixer\Fixer\Operator\LogicalOperatorsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\LogicalOperatorsFixer>
 *
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class LogicalOperatorsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php if ($a == "foo" && $b == "bar") {}',
            '<?php if ($a == "foo" and $b == "bar") {}',
        ];

        yield [
            '<?php if ($a == "foo" || $b == "bar") {}',
            '<?php if ($a == "foo" or $b == "bar") {}',
        ];

        yield [
            '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
            '<?php if ($a == "foo" and ($b == "bar" or $c == "baz")) {}',
        ];

        yield [
            '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
            '<?php if ($a == "foo" and ($b == "bar" || $c == "baz")) {}',
        ];

        yield [
            '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
            '<?php if ($a == "foo" && ($b == "bar" or $c == "baz")) {}',
        ];

        yield [
            '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
            '<?php if ($a == "foo" AND ($b == "bar" OR $c == "baz")) {}',
        ];

        yield [
            '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
            '<?php if ($a == "foo" and ($b == "bar" OR $c == "baz")) {}',
        ];

        yield [
            '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
            '<?php if ($a == "foo" AND ($b == "bar" or $c == "baz")) {}',
        ];
    }
}
