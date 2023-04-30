<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest\SimpleFixer;
use PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest\UnconfigurableFixer;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFixer
 */
final class AbstractFixerTest extends TestCase
{
    public function testDefaults(): void
    {
        $fixer = new UnconfigurableFixer();

        self::assertFalse($fixer->isRisky());
        self::assertTrue($fixer->supports(new \SplFileInfo(__FILE__)));
    }

    public function testConfigureUnconfigurable(): void
    {
        $fixer = new UnconfigurableFixer();

        self::assertSame(0, $fixer->getPriority());
        self::assertSame('unconfigurable', $fixer->getName());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot configure using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".');

        $fixer->configure(['foo' => 'bar']);
    }

    public function testGetConfigurationDefinitionUnconfigurable(): void
    {
        $fixer = new UnconfigurableFixer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(sprintf('Cannot get configuration definition using Abstract parent, child "%s" not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".', \get_class($fixer)));

        $fixer->getConfigurationDefinition();
    }

    public function testCreateConfigurationDefinitionUnconfigurable(): void
    {
        $fixer = new UnconfigurableFixer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot create configuration definition using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".');

        $fixer->doSomethingWithCreateConfigDefinition();
    }

    public function testSetWhitespacesConfigUnconfigurable(): void
    {
        $fixer = new UnconfigurableFixer();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot run method for class not implementing "PhpCsFixer\Fixer\WhitespacesAwareFixerInterface".');

        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());
    }

    public function testGetWhitespacesFixerConfig(): void
    {
        $fixer = new SimpleFixer();

        $config = $fixer->getWhitespacesConfig();

        self::assertSame('    ', $config->getIndent());
        self::assertSame("\n", $config->getLineEnding());

        $newConfig = new WhitespacesFixerConfig("\t", "\r\n");

        $fixer->setWhitespacesConfig($newConfig);

        $config = $fixer->getWhitespacesConfig();

        self::assertSame("\t", $config->getIndent());
        self::assertSame("\r\n", $config->getLineEnding());
    }
}
