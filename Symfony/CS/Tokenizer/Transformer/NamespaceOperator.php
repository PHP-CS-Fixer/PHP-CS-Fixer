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
use Symfony\CS\Tokenizer\Tokens;

/**
 * Transform `namespace` operator from T_NAMESPACE into CT_NAMESPACE_OPERATOR.
 *
 * @author Gregor Harlan <gharlan@web.de>
 */
class NamespaceOperator extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_NAMESPACE) as $index => $token) {
            $nextIndex = $tokens->getNextMeaningfulToken($index);
            $nextToken = $tokens[$nextIndex];

            if ($nextToken->equals(array(T_NS_SEPARATOR))) {
                $token->override(array(CT_NAMESPACE_OPERATOR, $token->getContent(), $token->getLine()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_NAMESPACE_OPERATOR');
    }
}
