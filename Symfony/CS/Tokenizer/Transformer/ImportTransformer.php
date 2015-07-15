<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer\Transformer;

use Symfony\CS\Tokenizer\AbstractTransformer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Transform const/function import tokens.
 *
 * Performed transformations:
 * - T_CONST into CT_CONST_IMPORT
 * - T_FUNCTION into CT_FUNCTION_IMPORT
 *
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class ImportTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_CONST_IMPORT', 'CT_FUNCTION_IMPORT');
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if (!$token->isGivenKind(array(T_CONST, T_FUNCTION))) {
            return;
        }

        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        if ($prevToken->isGivenKind(T_USE)) {
            $token->override(array(
                $token->isGivenKind(T_FUNCTION) ? CT_FUNCTION_IMPORT : CT_CONST_IMPORT,
                $token->getContent(),
            ));
        }
    }
}
