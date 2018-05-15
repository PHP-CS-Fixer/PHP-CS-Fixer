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
        self::assertInternalType('callable', new AllowedValueSubset(array('foo', 'bar')));
    }

    /**
     * @param mixed $inputValue
     * @param bool  $expectedResult
     *
     * @dataProvider provideInvokeCases
     */
    public function testInvoke($inputValue, $expectedResult)
    {
        $subset = new AllowedValueSubset(array('foo', 'bar'));

        self::assertSame($expectedResult, $subset($inputValue));
    }

    public function provideInvokeCases()
    {
        return array(
            array(
                array('foo', 'bar'),
                true,
            ),
            array(
                array('bar', 'foo'),
                true,
            ),
            array(
                array('foo'),
                true,
            ),
            array(
                array('bar'),
                true,
            ),
            array(
                array(),
                true,
            ),
            array(
                array('foo', 'bar', 'baz'),
                false,
            ),
            array(
                array('baz'),
                false,
            ),
            array(
                1,
                false,
            ),
            array(
                1.2,
                false,
            ),
            array(
                'foo',
                false,
            ),
            array(
                new \stdClass(),
                false,
            ),
            array(
                true,
                false,
            ),
            array(
                null,
                false,
            ),
        );
    }

    public function testGetAllowedValues()
    {
        $values = array('foo', 'bar');

        $subset = new AllowedValueSubset($values);

        $this->assertSame($values, $subset->getAllowedValues());
    }
}
