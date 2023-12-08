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

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\AccessibleObject\AccessibleObject;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
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
        $fixer = $this->createUnconfigurableFixerDouble();

        self::assertFalse($fixer->isRisky());
        self::assertTrue($fixer->supports(new \SplFileInfo(__FILE__)));
    }

    public function testConfigureUnconfigurable(): void
    {
        $fixer = $this->createUnconfigurableFixerDouble();

        self::assertSame(0, $fixer->getPriority());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot configure using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".');

        $fixer->configure(['foo' => 'bar']);
    }

    public function testGetConfigurationDefinitionUnconfigurable(): void
    {
        $fixer = $this->createUnconfigurableFixerDouble();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(sprintf('Cannot get configuration definition using Abstract parent, child "%s" not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".', \get_class($fixer)));

        $fixer->getConfigurationDefinition();
    }

    public function testCreateConfigurationDefinitionUnconfigurable(): void
    {
        $fixer = $this->createUnconfigurableFixerDouble();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot create configuration definition using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".');

        AccessibleObject::create($fixer)->createConfigurationDefinition();
    }

    public function testSetWhitespacesConfigUnconfigurable(): void
    {
        $fixer = $this->createUnconfigurableFixerDouble();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot run method for class not implementing "PhpCsFixer\Fixer\WhitespacesAwareFixerInterface".');

        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig());
    }

    public function testGetWhitespacesFixerConfig(): void
    {
        $fixer = $this->createWhitespacesAwareFixerDouble();

        $config = AccessibleObject::create($fixer)->whitespacesConfig;

        self::assertSame('    ', $config->getIndent());
        self::assertSame("\n", $config->getLineEnding());

        $newConfig = new WhitespacesFixerConfig("\t", "\r\n");

        $fixer->setWhitespacesConfig($newConfig);

        $config = AccessibleObject::create($fixer)->whitespacesConfig;

        self::assertSame("\t", $config->getIndent());
        self::assertSame("\r\n", $config->getLineEnding());
    }

    private function createWhitespacesAwareFixerDouble(): WhitespacesAwareFixerInterface
    {
        return new class() extends AbstractFixer implements WhitespacesAwareFixerInterface {
            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \BadMethodCallException('Not implemented.');
            }
        };
    }

    private function createUnconfigurableFixerDouble(): AbstractFixer
    {
        return new class() extends AbstractFixer {
            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return true;
            }

            protected function applyFix(\SplFileInfo $file, Tokens $tokens): void {}
        };
    }
}
