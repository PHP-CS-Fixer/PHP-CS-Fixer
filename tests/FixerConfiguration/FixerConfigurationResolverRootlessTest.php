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

use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use PhpCsFixer\FixerConfiguration\FixerOption;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless
 */
final class FixerConfigurationResolverRootlessTest extends \PHPUnit_Framework_TestCase
{
    public function testMapRootConfigurationTo()
    {
        $this->setExpectedException('LogicException', 'The "bar" option is not defined.');

        $configuration = new FixerConfigurationResolverRootless('bar', array(
            new FixerOption('foo', 'Bar.'),
        ));
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing "foo" at the root of the configuration is deprecated and will not be supported in 3.0, use "foo" => array(...) option instead.
     */
    public function testResolveWithMappedRoot()
    {
        $configuration = new FixerConfigurationResolverRootless('foo', array(
            new FixerOption('foo', 'Bar.'),
        ));
        $configuration->resolve(array('baz', 'qux'));
    }
}
