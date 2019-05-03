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

use PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\FixerConfiguration\FixerOptionInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\DeprecatedFixerOption
 */
final class DeprecatedFixerOptionTest extends TestCase
{
    public function testConstruct()
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        static::assertInstanceOf(FixerOptionInterface::class, $option);
        static::assertInstanceOf(DeprecatedFixerOptionInterface::class, $option);
    }

    public function testGetName()
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        static::assertSame('foo', $option->getName());
    }

    public function testGetDescription()
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'deprecated'
        );

        static::assertSame('Foo.', $option->getDescription());
    }

    /**
     * @param bool $isRequired
     *
     * @dataProvider provideHasDefaultCases
     */
    public function testHasDefault($isRequired)
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', $isRequired),
            'deprecated'
        );

        static::assertSame(!$isRequired, $option->hasDefault());
    }

    public function provideHasDefaultCases()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @param mixed $default
     *
     * @dataProvider provideGetDefaultCases
     */
    public function testGetDefault($default)
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', false, $default),
            'deprecated'
        );

        static::assertSame($default, $option->getDefault());
    }

    public function provideGetDefaultCases()
    {
        return [
            ['foo'],
            [true],
        ];
    }

    public function testGetAllowedTypes()
    {
        $allowedTypes = ['string', 'bool'];

        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', true, null, $allowedTypes),
            'deprecated'
        );

        static::assertSame($allowedTypes, $option->getAllowedTypes());
    }

    public function testGetAllowedValues()
    {
        $allowedValues = ['string', 'bool'];

        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.', true, null, [], $allowedValues),
            'deprecated'
        );

        static::assertSame($allowedValues, $option->getAllowedValues());
    }

    public function testGetNormalizer()
    {
        $normalizer = function () {};

        $decoratedOption = $this->prophesize(FixerOptionInterface::class);
        $decoratedOption->getNormalizer()->willReturn($normalizer);

        $option = new DeprecatedFixerOption(
            $decoratedOption->reveal(),
            'deprecated'
        );

        static::assertSame($normalizer, $option->getNormalizer());
    }

    public function testGetDeprecationMessage()
    {
        $option = new DeprecatedFixerOption(
            new FixerOption('foo', 'Foo.'),
            'Use option "bar" instead.'
        );

        static::assertSame('Use option "bar" instead.', $option->getDeprecationMessage());
    }
}
