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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

final class NoSpaceAroundDoubleColonFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There must be no space around double colons (also called Scope Resolution Operator or Paamayim Nekudotayim).',
            [new CodeSample("\n<?php echo Foo\\Bar :: class;\n")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOUBLE_COLON);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = \count($tokens) - 2; $index > 1; --$index) {
            if ($tokens[$index]->isGivenKind(T_DOUBLE_COLON)) {
                $this->removeSpace($tokens, $index, 1);
                $this->removeSpace($tokens, $index, -1);
            }
        }
    }

    /**
     * @param -1|1 $direction
     */
    private function removeSpace(Tokens $tokens, int $index, int $direction): void
    {
        if (!$tokens[$index + $direction]->isWhitespace()) {
            return;
        }

        if ($tokens[$tokens->getNonWhitespaceSibling($index, $direction)]->isComment()) {
            return;
        }

        $tokens->clearAt($index + $direction);
    }
}
