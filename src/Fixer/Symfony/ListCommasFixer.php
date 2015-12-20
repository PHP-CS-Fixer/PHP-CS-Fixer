<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ListCommasFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_LIST);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove trailing commas in list function calls.';
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_LIST)) {
                continue;
            }

            $openIndex = $tokens->getNextMeaningfulToken($index);
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            $markIndex = null;
            $prevIndex = $tokens->getPrevNonWhitespace($closeIndex);

            while ($tokens[$prevIndex]->equals(',')) {
                $markIndex = $prevIndex;
                $prevIndex = $tokens->getPrevNonWhitespace($prevIndex);
            }

            if (null !== $markIndex) {
                $tokens->clearRange(
                    $tokens->getPrevNonWhitespace($markIndex) + 1,
                    $closeIndex - 1
                );
            }
        }
    }
}
