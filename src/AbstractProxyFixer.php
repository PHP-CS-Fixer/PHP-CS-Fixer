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

namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractProxyFixer extends AbstractFixer
{
    /**
     * @var array<string, FixerInterface>
     */
    protected array $proxyFixers = [];

    public function __construct()
    {
        foreach (Utils::sortFixers($this->createProxyFixers()) as $proxyFixer) {
            $this->proxyFixers[$proxyFixer->getName()] = $proxyFixer;
        }

        parent::__construct();
    }

    public function isCandidate(Tokens $tokens): bool
    {
        foreach ($this->proxyFixers as $fixer) {
            if ($fixer->isCandidate($tokens)) {
                return true;
            }
        }

        return false;
    }

    public function isRisky(): bool
    {
        foreach ($this->proxyFixers as $fixer) {
            if ($fixer->isRisky()) {
                return true;
            }
        }

        return false;
    }

    public function getPriority(): int
    {
        if (\count($this->proxyFixers) > 1) {
            throw new \LogicException('You need to override this method to provide the priority of combined fixers.');
        }

        return reset($this->proxyFixers)->getPriority();
    }

    public function supports(\SplFileInfo $file): bool
    {
        foreach ($this->proxyFixers as $fixer) {
            if ($fixer->supports($file)) {
                return true;
            }
        }

        return false;
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $config): void
    {
        parent::setWhitespacesConfig($config);

        foreach ($this->proxyFixers as $fixer) {
            if ($fixer instanceof WhitespacesAwareFixerInterface) {
                $fixer->setWhitespacesConfig($config);
            }
        }
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->proxyFixers as $fixer) {
            $fixer->fix($file, $tokens);
        }
    }

    /**
     * @return list<FixerInterface>
     */
    abstract protected function createProxyFixers(): array;
}
