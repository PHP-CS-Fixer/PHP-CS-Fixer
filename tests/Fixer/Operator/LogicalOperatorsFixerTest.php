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
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\LogicalOperatorsFixer
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

    public function provideFixCases(): array
    {
        return [
            [
                '<?php if ($a == "foo" && $b == "bar") {}',
                '<?php if ($a == "foo" and $b == "bar") {}',
            ],
            [
                '<?php if ($a == "foo" || $b == "bar") {}',
                '<?php if ($a == "foo" or $b == "bar") {}',
            ],
            [
                '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
                '<?php if ($a == "foo" and ($b == "bar" or $c == "baz")) {}',
            ],
            [
                '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
                '<?php if ($a == "foo" and ($b == "bar" || $c == "baz")) {}',
            ],
            [
                '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
                '<?php if ($a == "foo" && ($b == "bar" or $c == "baz")) {}',
            ],
            [
                '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
                '<?php if ($a == "foo" AND ($b == "bar" OR $c == "baz")) {}',
            ],
            [
                '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
                '<?php if ($a == "foo" and ($b == "bar" OR $c == "baz")) {}',
            ],
            [
                '<?php if ($a == "foo" && ($b == "bar" || $c == "baz")) {}',
                '<?php if ($a == "foo" AND ($b == "bar" or $c == "baz")) {}',
            ],
        ];
    }
}
