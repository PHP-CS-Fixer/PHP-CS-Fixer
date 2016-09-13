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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractAlignFixerHelper;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
final class AlignEqualsFixerHelper extends AbstractAlignFixerHelper
{
    /**
     * {@inheritdoc}
     */
    protected function injectAlignmentPlaceholders(Tokens $tokens, $startAt, $endAt)
    {
        for ($index = $startAt; $index < $endAt; ++$index) {
            $token = $tokens[$index];

            if ($token->equals('=')) {
                $token->setContent(sprintf(self::ALIGNABLE_PLACEHOLDER, $this->deepestLevel).$token->getContent());
                continue;
            }

            if ($token->isGivenKind(T_FUNCTION)) {
                ++$this->deepestLevel;
                continue;
            }

            if ($token->equals('(')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
                continue;
            }

            if ($token->equals('[')) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $index);
                continue;
            }

            if ($token->isGivenKind(CT_ARRAY_SQUARE_BRACE_OPEN)) {
                $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                continue;
            }
        }
    }
}
