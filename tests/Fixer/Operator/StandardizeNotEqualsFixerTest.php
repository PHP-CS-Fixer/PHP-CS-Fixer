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
 * @covers \PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class StandardizeNotEqualsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php $a = ($b != $c);'];

        yield ['<?php $a = ($b != $c);', '<?php $a = ($b <> $c);'];
    }
}
