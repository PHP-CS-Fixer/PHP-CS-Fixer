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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ShortArraySyntaxFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_ARRAY);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ARRAY)) {
                continue;
            }

            $openIndex = $tokens->getNextTokenOfKind($index, array('('));
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

            $tokens->overrideAt($openIndex, array(CT::T_ARRAY_SQUARE_BRACE_OPEN, '['));
            $tokens->overrideAt($closeIndex, array(CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']'));

            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP arrays should use the PHP 5.4 short-syntax.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the BinaryOperatorSpacesFixer and TernarySpacesFixer.
        return 1;
    }
}
