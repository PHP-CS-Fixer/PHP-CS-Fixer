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
    public function testWithoutOptions()
    {
        $this->setExpectedException('LogicException', 'Options cannot be empty.');

        $configuration = new FixerConfigurationResolver(array());
    }

    public function testWithDuplicatesOptions()
    {
        $this->setExpectedException('LogicException', 'The "foo" option is defined multiple times.');

        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar-1.'),
            new FixerOption('foo', 'Bar-2.'),
        ));
    }

    public function testGetOptions()
    {
        $options = array(
            new FixerOption('foo', 'Bar.'),
            new FixerOption('baz', 'Qux.'),
        );
        $configuration = new FixerConfigurationResolver($options);

        $this->assertSame($options, $configuration->getOptions());
    }

    public function testResolve()
    {
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar.'),
        ));
        $this->assertSame(
            array('foo' => 'bar'),
            $configuration->resolve(array('foo' => 'bar'))
        );
    }

    public function testResolveWithMissingRequiredOption()
    {
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar.'),
        ));

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');
        $configuration->resolve(array());
    }

    public function testResolveWithDefault()
    {
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar.', false, 'baz'),
        ));

        $this->assertSame(
            array('foo' => 'baz'),
            $configuration->resolve(array())
        );
    }

    public function testResolveWithAllowedTypes()
    {
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar.', true, null, array('int')),
        ));

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
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar.', true, null, null, array(true, false)),
        ));

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
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('bar', 'Bar.'),
        ));

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException');
        $configuration->resolve(array('foo' => 'foooo'));
    }

    public function testResolveWithNormalizers()
    {
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar.', true, null, null, null, function (Options $options, $value) {
                return (int) $value;
            }),
        ));

        $this->assertSame(
            array('foo' => 1),
            $configuration->resolve(array('foo' => '1'))
        );

        $exception = new InvalidOptionsException('');
        $configuration = new FixerConfigurationResolver(array(
            new FixerOption('foo', 'Bar.', true, null, null, null, function (Options $options, $value) use ($exception) {
                throw $exception;
            }),
        ));

        $catched = null;
        try {
            $configuration->resolve(array('foo' => '1'));
        } catch (InvalidOptionsException $catched) {
        }

        $this->assertSame($exception, $catched);
    }
}
