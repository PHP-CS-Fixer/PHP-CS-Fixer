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

namespace PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class UnconfigurableFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        throw new \LogicException('Not implemented.');
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    public function doSomethingWithCreateConfigDefinition(): FixerConfigurationResolverInterface
    {
        return $this->createConfigurationDefinition();
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
    }
}
