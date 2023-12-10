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

namespace PhpCsFixer\Tests\Test;

/**
 * @internal
 */
final class TestCaseUtils
{
    /**
     * @param iterable<array{0: string, 1?: string}> $cases
     *
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function swapExpectedInputTestCases(iterable $cases): iterable
    {
        foreach ($cases as $case) {
            if (1 === \count($case)) {
                yield $case;

                continue;
            }

            [$case[0], $case[1]] = [$case[1], $case[0]];

            yield $case;
        }
    }
}
