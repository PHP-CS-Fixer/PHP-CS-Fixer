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

use PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\Tests\TestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\AliasedFixerOption
 */
final class AliasedFixerOptionTest extends TestCase
{
    /**
     * @param string $name
     *
     * @dataProvider provideGetNameCases
     */
    public function testGetName($name)
    {
        $option = new AliasedFixerOption(new FixerOption($name, 'Bar.'), 'baz');

        $this->assertSame($name, $option->getName());
    }

    public function provideGetNameCases()
    {
        return [
            ['foo'],
            ['bar'],
        ];
    }

    /**
     * @param string $description
     *
     * @dataProvider provideGetDescriptionCases
     */
    public function testGetDescription($description)
    {
        $option = new AliasedFixerOption(new FixerOption('foo', $description), 'baz');

        $this->assertSame($description, $option->getDescription());
    }

    public function provideGetDescriptionCases()
    {
        return [
            ['Foo.'],
            ['Bar.'],
        ];
    }

    /**
     * @param bool               $hasDefault
     * @param AliasedFixerOption $input
     *
     * @dataProvider provideHasDefaultCases
     */
    public function testHasDefault($hasDefault, AliasedFixerOption $input)
    {
        $this->assertSame($hasDefault, $input->hasDefault());
    }

    public function provideHasDefaultCases()
    {
        return [
            [
                false,
                new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz'),
            ],
            [
                true,
                new AliasedFixerOption(new FixerOption('foo', 'Bar.', false, 'baz'), 'baz'),
            ],
        ];
    }

    /**
     * @param string $default
     *
     * @dataProvider provideGetDefaultCases
     */
    public function testGetDefault($default)
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', false, $default), 'baz');

        $this->assertSame($default, $option->getDefault());
    }

    public function provideGetDefaultCases()
    {
        return [
            ['baz'],
            ['foo'],
        ];
    }

    public function testGetUndefinedDefault()
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz');

        $this->expectException('LogicException');
        $this->expectExceptionMessage('No default value defined.');
        $option->getDefault();
    }

    /**
     * @param null|array $allowedTypes
     *
     * @dataProvider provideGetAllowedTypesCases
     */
    public function testGetAllowedTypes($allowedTypes)
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, $allowedTypes), 'baz');

        $this->assertSame($allowedTypes, $option->getAllowedTypes());
    }

    public function provideGetAllowedTypesCases()
    {
        return [
            [null],
            [['bool']],
            [['bool', 'string']],
        ];
    }

    /**
     * @param null|array $allowedValues
     *
     * @dataProvider provideGetAllowedValuesCases
     */
    public function testGetAllowedValues($allowedValues)
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, $allowedValues), 'baz');

        $this->assertSame($allowedValues, $option->getAllowedValues());
    }

    public function provideGetAllowedValuesCases()
    {
        return [
            [null],
            [['baz']],
            [['baz', 'qux']],
        ];
    }

    public function testGetAllowedValuesClosure()
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, [function () {}]), 'baz');
        $allowedTypes = $option->getAllowedValues();
        $this->assertInternalType('array', $allowedTypes);
        $this->assertCount(1, $allowedTypes);
        $this->assertArrayHasKey(0, $allowedTypes);
        $this->assertInstanceOf('Closure', $allowedTypes[0]);
    }

    public function testGetNormalizers()
    {
        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.'), 'baz');
        $this->assertNull($option->getNormalizer());

        $option = new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, null, null, null, function () {}), 'baz');
        $this->assertInstanceOf('Closure', $option->getNormalizer());
    }

    /**
     * @param string $alias
     *
     * @dataProvider provideGetAliasCases
     */
    public function testGetAlias($alias)
    {
        $options = new AliasedFixerOption(new FixerOption('foo', 'Bar', true, null, null, null, null), $alias);

        $this->assertSame($alias, $options->getAlias());
    }

    public function provideGetAliasCases()
    {
        return [
            ['bar'],
            ['baz'],
        ];
    }

    public function testRequiredWithDefaultValue()
    {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Required options cannot have a default value.');

        new AliasedFixerOption(new FixerOption('foo', 'Bar.', true, false), 'baz');
    }
}
