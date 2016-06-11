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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class CombineConsecutiveIssetsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_ISSET) as $index => $token) {
            $this->findAndReplaceTrailingIssets($tokens, $index);
        }

        return $tokens->generateCode();
    }

    /**
     * @param Tokens $tokens
     * @param int    $firstIssetIndex
     */
    private function findAndReplaceTrailingIssets(Tokens $tokens, $firstIssetIndex)
    {
        $openParenthesisIndex = $tokens->getNextMeaningfulToken($firstIssetIndex);
        if (!$tokens[$openParenthesisIndex]->equals('(')) {
            return;
        }
        $closeParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesisIndex);

        $neededSequence = $tokens->findSequence(array(')', array(T_BOOLEAN_AND), array(T_ISSET), '('), $closeParenthesisIndex);
        // Replace `) && isset(` by `, `
        if ($neededSequence) {
            $neededSequenceIndexes = array_keys($neededSequence);
            $tokens->clearRange(current($neededSequenceIndexes), end($neededSequenceIndexes));
            $tokens[end($neededSequenceIndexes)]->setContent(',');
            $tokens->insertAt(end($neededSequenceIndexes) + 1, new Token(array(T_WHITESPACE, ' ')));

            // Call again the method if more trailing issets.
            $this->findAndReplaceTrailingIssets($tokens, $firstIssetIndex);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Calling isset on multiple items should be done in one call.';
    }
}
