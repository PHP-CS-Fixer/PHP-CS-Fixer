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

namespace PhpCsFixer\Tests\Fixer;

use PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest\UnconfigurableFixer;
use PhpCsFixer\WhitespacesFixerConfig;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFixer
 */
final class AbstractFixerTest extends TestCase
{
    public function testConfigureUnconfigurable()
    {
        $fixer = new UnconfigurableFixer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot configure using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface".');

        $fixer->configure(['foo' => 'bar']);
    }

    public function testGetConfigurationDefinitionUnconfigurable()
    {
        $fixer = new UnconfigurableFixer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot get configuration definition using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface".');

        $fixer->getConfigurationDefinition();
    }

    public function testCreateConfigurationDefinitionUnconfigurable()
    {
        $fixer = new UnconfigurableFixer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot create configuration definition using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface".');

        $fixer->doSomethingWithCreateConfigDefinition();
    }

    public function testSetWhitespacesConfigUnconfigurable()
    {
        $fixer = new UnconfigurableFixer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot run method for class not implementing "PhpCsFixer\Fixer\WhitespacesAwareFixerInterface".');

        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());
    }
}
