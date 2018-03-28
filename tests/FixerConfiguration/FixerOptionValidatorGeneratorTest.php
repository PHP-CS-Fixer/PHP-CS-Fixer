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

namespace PhpCsFixer\Tests\FixerConfiguration;

use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\Tests\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator
 */
final class FixerOptionValidatorGeneratorTest extends TestCase
{
    /**
     * @param bool  $expected
     * @param array $allowed
     * @param mixed $input
     *
     * @dataProvider provideAllowedValueIsSubsetOfCases
     */
    public function testAllowedValueIsSubsetOf($expected, array $allowed, $input)
    {
        $generator = new FixerOptionValidatorGenerator();
        $allowed = $generator->allowedValueIsSubsetOf($allowed);

        $this->assertSame($expected, $allowed($input));
    }

    public function provideAllowedValueIsSubsetOfCases()
    {
        return [
            [true, [1, 2, 3], [1]],
            [true, [1, 2, 3], [3, 1]],
            [false, [1, 2, 3], ['1']],
            [false, [1, 2, 3], [3, '1']],
            [false, [1], [true]],
        ];
    }
}
