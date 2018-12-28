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
use PhpCsFixer\Tokenizer\Tokens;

final class FunctionReferenceTestFixer extends AbstractFunctionReferenceFixer
{
    public function getDefinition()
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function isCandidate(Tokens $tokens)
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function findTest($functionNameToSearch, Tokens $tokens, $start = 0, $end = null)
    {
        return parent::find($functionNameToSearch, $tokens, $start, $end);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        throw new \BadMethodCallException('Not implemented.');
    }
}
