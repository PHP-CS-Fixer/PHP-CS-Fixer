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

namespace PhpCsFixer\Tests\Fixtures\Test\AbstractProxyFixerTest;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class SimpleFixer implements FixerInterface
{
    private bool $isCandidate;

    private bool $isRisky;

    private bool $supports;

    private int $priority;

    private int $fixCalled = 0;

    private static int $callCount = 1;

    public function __construct(
        bool $isCandidate,
        bool $isRisky = false,
        bool $supports = false,
        int $priority = 999
    ) {
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

    public function isFixCalled(): int
    {
        return $this->fixCalled;
    }

    public function isRisky(): bool
    {
        return $this->isRisky;
    }

    public function supports(\SplFileInfo $file): bool
    {
        return $this->supports;
    }
}
