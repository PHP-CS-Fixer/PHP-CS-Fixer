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

namespace PhpCsFixer\Tests\Double;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

class FixerDoubleFactory
{
    /**
     * @return AbstractFixer|ConfigurableFixerInterface
     */
    public static function createConfigurableFixer()
    {
        return new class() extends AbstractFixer implements ConfigurableFixerInterface {
            /**
             * @param array<mixed> $configuration
             */
            public function configure(array $configuration): void
            {
                $this->configuration = $configuration;
            }

            public function getConfigurationDefinition(): FixerConfigurationResolverInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \LogicException('Not implemented.');
            }

            public function isRisky(): bool
            {
                throw new \LogicException('Not implemented.');
            }

            protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \LogicException('Not implemented.');
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function getName(): string
            {
                return 'configurable';
            }

            public function getPriority(): int
            {
                throw new \LogicException('Not implemented.');
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }

    public static function createNamed(
        string $name,
        int $priority = 0
    ): FixerInterface {
        return new class($name, $priority) implements FixerInterface {
            private string $name;
            private int $priority;

            public function __construct(string $name, int $priority)
            {
                $this->name = $name;
                $this->priority = $priority;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return true;
            }

            public function isRisky(): bool
            {
                return true;
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void {}

            public function getDefinition(): FixerDefinitionInterface
            {
                return new FixerDefinition('Fixes stuff.', []);
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function supports(\SplFileInfo $file): bool
            {
                return true;
            }
        };
    }

    public static function createUnconfigurableFixer(): AbstractFixer
    {
        return new class() extends AbstractFixer {
            public function getName(): string
            {
                return 'unconfigurable';
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \LogicException('Not implemented.');
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return true;
            }

            protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \BadMethodCallException('Not implemented.');
            }
        };
    }

    /**
     * @return AbstractFixer|WhitespacesAwareFixerInterface
     */
    public static function createWhitespacesAwareFixer()
    {
        return new class() extends AbstractFixer implements WhitespacesAwareFixerInterface {
            public function getName(): string
            {
                return uniqid('whitespace_aware_double_');
            }

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

    public static function createSimple(
        bool $isCandidate = true,
        bool $isRisky = false,
        bool $supports = false,
        int $priority = 0
    ): FixerInterface {
        return new class($isCandidate, $isRisky, $supports, $priority) implements FixerInterface {
            private bool $isCandidate;
            private bool $isRisky;
            private bool $supports;
            private int $priority;
            private int $fixCalled = 0;
            private static int $callCount = 1;

            public function __construct(
                bool $isCandidate,
                bool $isRisky,
                bool $supports,
                int $priority
            ) {
                $this->isCandidate = $isCandidate;
                $this->isRisky = $isRisky;
                $this->supports = $supports;
                $this->priority = $priority;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return $this->isCandidate;
            }

            public function isRisky(): bool
            {
                return $this->isRisky;
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                if (0 !== $this->fixCalled) {
                    throw new \RuntimeException('Fixer called multiple times.');
                }

                $this->fixCalled = self::$callCount;
                ++self::$callCount;
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function getName(): string
            {
                return uniqid('abstract_proxy_double_');
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function supports(\SplFileInfo $file): bool
            {
                return $this->supports;
            }
        };
    }
}
