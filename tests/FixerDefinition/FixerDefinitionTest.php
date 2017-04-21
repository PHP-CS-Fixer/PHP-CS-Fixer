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

namespace PhpCsFixer\Tests\FixerDefinition;

use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @internal
 *
 * @covers \PhpCsFixer\FixerDefinition\FixerDefinition
 */
final class FixerDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSummary()
    {
        $definition = new FixerDefinition('Foo', []);

        $this->assertSame('Foo', $definition->getSummary());
    }

    public function testGetCodeSamples()
    {
        $definition = new FixerDefinition('', ['Bar', 'Baz']);

        $this->assertSame(['Bar', 'Baz'], $definition->getCodeSamples());
    }

    public function testGetDescription()
    {
        $definition = new FixerDefinition('', []);

        $this->assertNull($definition->getDescription());

        $definition = new FixerDefinition('', [], 'Foo');

        $this->assertSame('Foo', $definition->getDescription());
    }

    /**
     * @group legacy
     * @expectedDeprecation PhpCsFixer\FixerDefinition\FixerDefinition::getConfigurationDescription is deprecated and will be removed in 3.0.
     */
    public function testGetConfigurationDescription()
    {
        $definition = new FixerDefinition('', []);

        $this->assertNull($definition->getConfigurationDescription());

        $definition = new FixerDefinition('', [], null, 'Foo');

        $this->assertNull($definition->getConfigurationDescription());

        $definition = new FixerDefinition('', [], null, 'Foo', []);

        $this->assertSame('Foo', $definition->getConfigurationDescription());
    }

    /**
     * @group legacy
     * @expectedDeprecation Argument #5 of FixerDefinition::__construct() is deprecated and will be removed in 3.0.
     * @expectedDeprecation PhpCsFixer\FixerDefinition\FixerDefinition::getDefaultConfiguration is deprecated and will be removed in 3.0.
     */
    public function testGetDefaultConfiguration()
    {
        $definition = new FixerDefinition('', []);

        $this->assertNull($definition->getDefaultConfiguration());

        $definition = new FixerDefinition('', [], null, null, ['Foo', 'Bar']);

        $this->assertSame(['Foo', 'Bar'], $definition->getDefaultConfiguration());
    }

    public function testGetRiskyDescription()
    {
        $definition = new FixerDefinition('', []);

        $this->assertNull($definition->getRiskyDescription());

        $definition = new FixerDefinition('', [], null, 'Foo');

        $this->assertSame('Foo', $definition->getRiskyDescription());
    }

    /**
     * @group legacy
     * @expectedDeprecation Arguments #5 and #6 of FixerDefinition::__construct() are deprecated and will be removed in 3.0, use argument #4 instead.
     */
    public function testLegacyGetRiskyDescription()
    {
        $definition = new FixerDefinition('', [], null, null, null, 'Foo');

        $this->assertSame('Foo', $definition->getRiskyDescription());
    }
}
