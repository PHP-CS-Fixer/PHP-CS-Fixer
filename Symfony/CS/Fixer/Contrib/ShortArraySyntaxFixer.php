<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ShortArraySyntaxFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_ARRAY) as $index => $token) {
            $openIndex = $tokens->getNextTokenOfKind($index, array('('));
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

            $token->clear();
            $tokens[$openIndex]->override(array(CT_ARRAY_SQUARE_BRACE_OPEN, '['));
            $tokens[$closeIndex]->override(array(CT_ARRAY_SQUARE_BRACE_CLOSE, ']'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP array\'s should use the PHP 5.4 short-syntax.';
    }
}
