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

use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOption;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver
 */
final class FixerConfigurationResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutOptions()
    {
        $this->setExpectedException(\LogicException::class, 'Options cannot be empty.');

        $configuration = new FixerConfigurationResolver([]);
    }

    public function testWithDuplicatesOptions()
    {
        $this->setExpectedException(\LogicException::class, 'The "foo" option is defined multiple times.');

        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar-1.'),
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

        $this->assertSame($options, $configuration->getOptions());
    }

    public function testResolve()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.'),
        ]);
        $this->assertSame(
            ['foo' => 'bar'],
            $configuration->resolve(['foo' => 'bar'])
        );
    }

    public function testResolveWithMissingRequiredOption()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.'),
        ]);

        $this->setExpectedException(\Symfony\Component\OptionsResolver\Exception\MissingOptionsException::class);
        $configuration->resolve([]);
    }

    public function testResolveWithDefault()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', false, 'baz'),
        ]);

        $this->assertSame(
            ['foo' => 'baz'],
            $configuration->resolve([])
        );
    }

    public function testResolveWithAllowedTypes()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, ['int']),
        ]);

        $this->assertSame(
            ['foo' => 1],
            $configuration->resolve(['foo' => 1])
        );

        $this->setExpectedException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $this->assertSame(
            ['foo' => 1],
            $configuration->resolve(['foo' => '1'])
        );
    }

    public function testResolveWithAllowedValues()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, null, [true, false]),
        ]);

        $this->assertSame(
            ['foo' => true],
            $configuration->resolve(['foo' => true])
        );

        $this->setExpectedException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $this->assertSame(
            ['foo' => 1],
            $configuration->resolve(['foo' => 1])
        );
    }

    public function testResolveWithUndefinedOption()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('bar', 'Bar.'),
        ]);

        $this->setExpectedException(\Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException::class);
        $configuration->resolve(['foo' => 'foooo']);
    }

    public function testResolveWithNormalizers()
    {
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, null, null, function (Options $options, $value) {
                return (int) $value;
            }),
        ]);

        $this->assertSame(
            ['foo' => 1],
            $configuration->resolve(['foo' => '1'])
        );

        $exception = new InvalidOptionsException('');
        $configuration = new FixerConfigurationResolver([
            new FixerOption('foo', 'Bar.', true, null, null, null, function (Options $options, $value) use ($exception) {
                throw $exception;
            }),
        ]);

        $catched = null;
        try {
            $configuration->resolve(['foo' => '1']);
        } catch (InvalidOptionsException $catched) {
        }

        $this->assertSame($exception, $catched);
    }
}
