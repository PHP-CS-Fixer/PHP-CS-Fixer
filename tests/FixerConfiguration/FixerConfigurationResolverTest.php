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
 */
final class FixerConfigurationResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testAddOption()
    {
        $configuration = new FixerConfigurationResolver();
        $this->assertSame(
            $configuration,
            $configuration->addOption(new FixerOption('foo', 'Bar.'))
        );

        $this->setExpectedException('LogicException', 'The "foo" option is already defined.');
        $this->assertSame(
            $configuration,
            $configuration->addOption(new FixerOption('foo', 'Bar.'))
        );
    }

    public function testGetOptions()
    {
        $configuration = new FixerConfigurationResolver();

        $options = array();
        $this->assertSame($options, $configuration->getOptions());

        $options[] = new FixerOption('foo', 'Bar.');
        $configuration->addOption(end($options));
        $this->assertSame($options, $configuration->getOptions());

        $options[] = new FixerOption('baz', 'Qux.');
        $configuration->addOption(end($options));
        $this->assertSame($options, $configuration->getOptions());
    }

    public function testMapRootConfigurationTo()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption(new FixerOption('foo', 'Bar.'));
        $this->assertSame($configuration, $configuration->mapRootConfigurationTo('foo'));

        $this->setExpectedException('LogicException', 'The "bar" option is not defined.');
        $configuration->mapRootConfigurationTo('bar');
    }

    public function testResolve()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption(new FixerOption('foo', 'Bar.'));
        $this->assertSame(
            array('foo' => 'bar'),
            $configuration->resolve(array('foo' => 'bar'))
        );
    }

    public function testResolveWithMissingRequiredOption()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption(new FixerOption('foo', 'Bar.'));

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');
        $configuration->resolve(array());
    }

    public function testResolveWithDefault()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption($option = new FixerOption('foo', 'Bar.'));
        $option->setDefault('baz');

        $this->assertSame(
            array('foo' => 'baz'),
            $configuration->resolve(array())
        );
    }

    public function testResolveWithAllowedTypes()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption($option = new FixerOption('foo', 'Bar.'));
        $option->setAllowedTypes('int');

        $this->assertSame(
            array('foo' => 1),
            $configuration->resolve(array('foo' => 1))
        );

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\InvalidOptionsException');
        $this->assertSame(
            array('foo' => 1),
            $configuration->resolve(array('foo' => '1'))
        );
    }

    public function testResolveWithAllowedValues()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption($option = new FixerOption('foo', 'Bar.'));
        $option->setAllowedValues(true, false);

        $this->assertSame(
            array('foo' => true),
            $configuration->resolve(array('foo' => true))
        );

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\InvalidOptionsException');
        $this->assertSame(
            array('foo' => 1),
            $configuration->resolve(array('foo' => 1))
        );
    }

    public function testResolveWithUndefinedOption()
    {
        $configuration = new FixerConfigurationResolver();

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException');
        $configuration->resolve(array('foo' => 'bar'));
    }

    public function testResolveWithNormalizers()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption($option = new FixerOption('foo', 'Bar.'));
        $option->setNormalizer(function (Options $options, $value) {
            return (int) $value;
        });
        $this->assertSame(
            array('foo' => 1),
            $configuration->resolve(array('foo' => '1'))
        );

        $exception = new InvalidOptionsException('');
        $option->setNormalizer(function (Options $options, $value) use ($exception) {
            throw $exception;
        });

        $catched = null;
        try {
            $configuration->resolve(array('foo' => '1'));
        } catch (InvalidOptionsException $catched) {
        }

        $this->assertSame($exception, $catched);
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing "foo" at the root of the configuration is deprecated and will not be supported in 3.0, use "foo" => array(...) option instead.
     */
    public function testResolveWithMappedRoot()
    {
        $configuration = new FixerConfigurationResolver();
        $configuration->addOption($option = new FixerOption('foo', 'Bar.'));
        $configuration->mapRootConfigurationTo('foo');
        $configuration->resolve(array('baz', 'qux'));
    }
}
