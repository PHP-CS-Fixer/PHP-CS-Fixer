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

use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\AllowedValueSubset
 */
final class AllowedValueSubsetTest extends TestCase
{
    public function testConstructor()
    {
        self::assertInternalType('callable', new AllowedValueSubset(['foo', 'bar']));
    }

    /**
     * @param mixed $inputValue
     * @param bool  $expectedResult
     *
     * @dataProvider provideInvokeCases
     */
    public function testInvoke($inputValue, $expectedResult)
    {
        $subset = new AllowedValueSubset(['foo', 'bar']);

        self::assertSame($expectedResult, $subset($inputValue));
    }

    public function provideInvokeCases()
    {
        return [
            [
                ['foo', 'bar'],
                true,
            ],
            [
                ['bar', 'foo'],
                true,
            ],
            [
                ['foo'],
                true,
            ],
            [
                ['bar'],
                true,
            ],
            [
                [],
                true,
            ],
            [
                ['foo', 'bar', 'baz'],
                false,
            ],
            [
                ['baz'],
                false,
            ],
            [
                1,
                false,
            ],
            [
                1.2,
                false,
            ],
            [
                'foo',
                false,
            ],
            [
                new \stdClass(),
                false,
            ],
            [
                true,
                false,
            ],
            [
                null,
                false,
            ],
        ];
    }

    public function testGetAllowedValues()
    {
        $values = ['foo', 'bar'];

        $subset = new AllowedValueSubset($values);

        $this->assertSame($values, $subset->getAllowedValues());
    }
}
