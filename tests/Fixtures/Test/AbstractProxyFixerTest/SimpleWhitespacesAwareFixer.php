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

use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 */
final class SimpleWhitespacesAwareFixer implements WhitespacesAwareFixerInterface
{
    /**
     * @var null|WhitespacesFixerConfig
     */
    private $whitespacesConfig;

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

    public function getWhitespacesFixerConfig(): ?WhitespacesFixerConfig
    {
        return $this->whitespacesConfig;
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
}
