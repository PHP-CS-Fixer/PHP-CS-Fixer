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
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOption;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

/**
 * @internal
 *
 * @group legacy
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver
 */
final class FixerConfigurationResolverTest extends TestCase
{
    public function testWithoutOptions()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Options cannot be empty.');

        new FixerConfigurationResolver([]);
    }

    public function testWithDuplicatesOptions()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "foo" option is defined multiple times.');

        new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar-1.'),
            new FixerOption('foo', 'Bar-2.'),
        ]);
    }

    public function testWithDuplicateAliasOptions()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "foo" option is defined multiple times.');

        new FixerConfigurationResolver([
            new AliasedFixerOption(new FixerOption('foo', 'Bar-1.'), 'baz'),
            new FixerOption('foo', 'Bar-2.'),
        ]);
    }

    public function testGetOptions()
    {
        $options = [
            new FixerOption('foo', 'Bar.'),
            new FixerOption('baz', 'Qux.'),
        ];
        $configuration = new FixerConfigurationResolver($options);

        static::assertSame($options, $configuration->getOptions());
    }

    public function testResolve()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.'),
        ]);
        static::assertSame(
            ['foo' => 'bar'],
            $configuration->resolve(['foo' => 'bar'])
        );
    }

    public function testResolveWithMissingRequiredOption()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.'),
        ]);

        $this->expectException(\Symfony\Component\OptionsResolver\Exception\MissingOptionsException::class);
        $configuration->resolve([]);
    }

    public function testResolveWithDefault()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', false, 'baz'),
        ]);

        static::assertSame(
            ['foo' => 'baz'],
            $configuration->resolve([])
        );
    }

    public function testResolveWithAllowedTypes()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, ['int']),
        ]);

        static::assertSame(
            ['foo' => 1],
            $configuration->resolve(['foo' => 1])
        );

        $this->expectException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $configuration->resolve(['foo' => '1']);
    }

    public function testResolveWithAllowedValues()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, null, [true, false]),
        ]);

        static::assertSame(
            ['foo' => true],
            $configuration->resolve(['foo' => true])
        );

        $this->expectException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $configuration->resolve(['foo' => 1]);
    }

    public function testResolveWithAllowedValuesSubset()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, null, [new AllowedValueSubset(['foo', 'bar'])]),
        ]);

        static::assertSame(
            ['foo' => ['bar']],
            $configuration->resolve(['foo' => ['bar']])
        );

        $this->expectException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $configuration->resolve(['foo' => ['baz']]);
    }

    public function testResolveWithUndefinedOption()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('bar', 'Bar.'),
        ]);

        $this->expectException(\Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException::class);
        $configuration->resolve(['foo' => 'foooo']);
    }

    public function testResolveWithNormalizers()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, null, null, static function (Options $options, $value) {
                return (int) $value;
            }),
        ]);

        static::assertSame(
            ['foo' => 1],
            $configuration->resolve(['foo' => '1'])
        );

        $exception = new InvalidOptionsException('');
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, null, null, static function (Options $options, $value) use ($exception) {
                throw $exception;
            }),
        ]);

        $caught = null;

        try {
            $configuration->resolve(['foo' => '1']);
        } catch (InvalidOptionsException $caught) {
        }

        static::assertSame($exception, $caught);
    }

    public function testResolveWithAliasedDuplicateConfig()
    {
        $configuration = new FixerConfigurationResolver([
            new AliasedFixerOption(new FixerOption('bar', 'Bar.'), 'baz'),
        ]);

        $this->expectException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $this->expectExceptionMessage('Aliased option bar/baz is passed multiple times');

        $configuration->resolve([
            'bar' => '1',
            'baz' => '2',
        ]);
    }

    /**
     * @expectedDeprecation Option "baz" is deprecated, use "bar" instead.
     */
    public function testResolveWithDeprecatedAlias()
    {
        $configuration = new FixerConfigurationResolver([
            new AliasedFixerOption(new FixerOption('bar', 'Bar.'), 'baz'),
        ]);

        $configuration->resolve([
            'baz' => '1',
        ]);
    }
}
