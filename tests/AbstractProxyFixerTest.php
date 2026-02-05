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

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractProxyFixer
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AbstractProxyFixerTest extends TestCase
{
    public function testCandidate(): void
    {
        $proxyFixer = $this->createProxyFixerDouble([$this->createFixerDouble(true)]);
        self::assertTrue($proxyFixer->isCandidate(new Tokens()));

        $proxyFixer = $this->createProxyFixerDouble([$this->createFixerDouble(false)]);
        self::assertFalse($proxyFixer->isCandidate(new Tokens()));

        $proxyFixer = $this->createProxyFixerDouble([
            $this->createFixerDouble(false),
            $this->createFixerDouble(true),
        ]);

        self::assertTrue($proxyFixer->isCandidate(new Tokens()));
    }

    public function testRisky(): void
    {
        $proxyFixer = $this->createProxyFixerDouble([$this->createFixerDouble(true, false)]);
        self::assertFalse($proxyFixer->isRisky());

        $proxyFixer = $this->createProxyFixerDouble([$this->createFixerDouble(true, true)]);
        self::assertTrue($proxyFixer->isRisky());

        $proxyFixer = $this->createProxyFixerDouble([
            $this->createFixerDouble(true, false),
            $this->createFixerDouble(true, true),
            $this->createFixerDouble(true, false),
        ]);

        self::assertTrue($proxyFixer->isRisky());
    }

    public function testSupports(): void
    {
        $file = new \SplFileInfo(__FILE__);

        $proxyFixer = $this->createProxyFixerDouble([$this->createFixerDouble(true, false, false)]);
        self::assertFalse($proxyFixer->supports($file));

        $proxyFixer = $this->createProxyFixerDouble([$this->createFixerDouble(true, true, true)]);
        self::assertTrue($proxyFixer->supports($file));

        $proxyFixer = $this->createProxyFixerDouble([
            $this->createFixerDouble(true, false, false),
            $this->createFixerDouble(true, true, false),
            $this->createFixerDouble(true, false, true),
        ]);

        self::assertTrue($proxyFixer->supports($file));
    }

    public function testPrioritySingleFixer(): void
    {
        $proxyFixer = $this->createProxyFixerDouble([
            $this->createFixerDouble(true, false, false, 123),
        ]);
        self::assertSame(123, $proxyFixer->getPriority());
    }

    public function testPriorityMultipleFixersNotSet(): void
    {
        $proxyFixer = $this->createProxyFixerDouble([
            $this->createFixerDouble(true),
            $this->createFixerDouble(true, true),
            $this->createFixerDouble(true, false, true),
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You need to override this method to provide the priority of combined fixers.');

        $proxyFixer->getPriority();
    }

    public function testWhitespacesConfig(): void
    {
        $config = new WhitespacesFixerConfig();
        $whitespacesAwareFixer = $this->createWhitespacesAwareFixerDouble();

        $proxyFixer = $this->createProxyFixerDouble([
            $this->createFixerDouble(true, true),
            $whitespacesAwareFixer,
            $this->createFixerDouble(true, false, true),
        ]);

        $proxyFixer->setWhitespacesConfig($config);

        self::assertSame(
            $config,
            \Closure::bind(static fn ($fixer): WhitespacesFixerConfig => $fixer->whitespacesConfig, null, \get_class($whitespacesAwareFixer))($whitespacesAwareFixer),
        );
    }

    public function testApplyFixInPriorityOrder(): void
    {
        $fixer1 = $this->createFixerDouble(true, false, true, 1);
        $fixer2 = $this->createFixerDouble(true, false, true, 10);

        $proxyFixer = $this->createProxyFixerDouble([$fixer1, $fixer2]);
        $proxyFixer->fix(new \SplFileInfo(__FILE__), Tokens::fromCode('<?php echo 1;'));

        self::assertSame(2, \Closure::bind(static fn ($fixer): int => $fixer->fixCalled, null, \get_class($fixer1))($fixer1));
        self::assertSame(1, \Closure::bind(static fn ($fixer): int => $fixer->fixCalled, null, \get_class($fixer2))($fixer2));
    }

    private function createFixerDouble(
        bool $isCandidate,
        bool $isRisky = false,
        bool $supports = false,
        int $priority = 999
    ): FixerInterface {
        return new class($isCandidate, $isRisky, $supports, $priority) implements FixerInterface {
            private bool $isCandidate;
            private bool $isRisky;
            private bool $supports;
            private int $priority;
            private int $fixCalled = 0;
            private static int $callCount = 1;

            public function __construct(bool $isCandidate, bool $isRisky, bool $supports, int $priority)
            {
                $this->isCandidate = $isCandidate;
                $this->isRisky = $isRisky;
                $this->supports = $supports;
                $this->priority = $priority;
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
                return uniqid('abstract_proxy_test_');
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                return $this->isCandidate;
            }

            public function isRisky(): bool
            {
                return $this->isRisky;
            }

            public function supports(\SplFileInfo $file): bool
            {
                return $this->supports;
            }
        };
    }

    private function createWhitespacesAwareFixerDouble(): WhitespacesAwareFixerInterface
    {
        return new class implements WhitespacesAwareFixerInterface {
            /** @phpstan-ignore-next-line to not complain that property is never read */
            private WhitespacesFixerConfig $whitespacesConfig;

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function fix(\SplFileInfo $file, Tokens $tokens): void
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function getName(): string
            {
                return uniqid('abstract_proxy_aware_test_');
            }

            public function getPriority(): int
            {
                return 1;
            }

            public function isCandidate(Tokens $tokens): bool
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function isRisky(): bool
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            public function setWhitespacesConfig(WhitespacesFixerConfig $config): void
            {
                $this->whitespacesConfig = $config;
            }

            public function supports(\SplFileInfo $file): bool
            {
                throw new \BadMethodCallException('Not implemented.');
            }
        };
    }

    /**
     * @param non-empty-list<FixerInterface> $fixers
     */
    private function createProxyFixerDouble(array $fixers): AbstractProxyFixer
    {
        return new class($fixers) extends AbstractProxyFixer implements WhitespacesAwareFixerInterface {
            /**
             * @var non-empty-list<FixerInterface>
             */
            private array $fixers;

            /**
             * @param non-empty-list<FixerInterface> $fixers
             */
            public function __construct(array $fixers)
            {
                $this->fixers = $fixers;

                parent::__construct();
            }

            public function getDefinition(): FixerDefinitionInterface
            {
                throw new \BadMethodCallException('Not implemented.');
            }

            protected function createProxyFixers(): array
            {
                return $this->fixers;
            }
        };
    }
}
