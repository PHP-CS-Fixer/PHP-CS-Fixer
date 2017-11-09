<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixtures\Test\AbstractFixerTest;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 */
final class UnconfigurableFixer extends AbstractFixer
{
    public function getDefinition()
    {
    }

    public function isCandidate(Tokens $tokens)
    {
    }

    public function doSomethingWithCreateConfigDefinition()
    {
        return $this->createConfigurationDefinition();
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
    }
}
