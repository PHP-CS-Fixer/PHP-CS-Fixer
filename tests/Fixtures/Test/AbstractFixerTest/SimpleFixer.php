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

namespace PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 */
final class SimpleFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function getWhitespacesConfig(): WhitespacesFixerConfig
    {
        return $this->whitespacesConfig;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        throw new \BadMethodCallException('Not implemented.');
    }
}
