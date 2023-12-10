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

namespace PhpCsFixer\Tests\Fixtures;

use PhpCsFixer\AbstractFunctionReferenceFixer;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

final class FunctionReferenceTestFixer extends AbstractFunctionReferenceFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function isCandidate(Tokens $tokens): bool
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function findTest(string $functionNameToSearch, Tokens $tokens, int $start = 0, ?int $end = null): ?array
    {
        return parent::find($functionNameToSearch, $tokens, $start, $end);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        throw new \BadMethodCallException('Not implemented.');
    }
}
