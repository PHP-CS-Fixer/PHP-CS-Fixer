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
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ARRAY)) {
                continue;
            }

            $openIndex = $tokens->getNextTokenOfKind($index, array('('));
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

            $token->clear();

            $tokens->overrideAt($openIndex, array(CT_ARRAY_SQUARE_BRACE_OPEN, '['));
            $tokens->overrideAt($closeIndex, array(CT_ARRAY_SQUARE_BRACE_CLOSE, ']'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP arrays should use the PHP 5.4 short-syntax.';
    }
}
