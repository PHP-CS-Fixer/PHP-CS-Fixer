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

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transform NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED and T_NAME_RELATIVE into T_NAMESPACE T_NS_SEPARATOR T_STRING.
 *
 * @author SpacePossum
 *
 * @internal
 */
final class NameQualifiedTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 1; // must run before NamespaceOperatorTransformer
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId()
    {
        return 80000;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, $index)
    {
        if ($token->isGivenKind([T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
            return $this->transformQualified($tokens, $token, $index);
        }

        if ($token->isGivenKind(T_NAME_RELATIVE)) {
            return $this->transformRelative($tokens, $token, $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDeprecatedCustomTokens()
    {
        return [];
    }

    private function transformQualified(Tokens $tokens, Token $token, $index)
    {
        $parts = explode('\\', $token->getContent());
        $newTokens = [];

        if ('' === $parts[0]) {
            $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
            array_shift($parts);
        }

        foreach ($parts as $part) {
            $newTokens[] = new Token([T_STRING, $part]);
            $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
        }

        array_pop($newTokens);

        $tokens->overrideRange($index, $index, $newTokens);
    }

    private function transformRelative(Tokens $tokens, Token $token, $index)
    {
        $parts = explode('\\', $token->getContent());
        $newTokens = [
            new Token([T_NAMESPACE, array_shift($parts)]),
            new Token([T_NS_SEPARATOR, '\\']),
        ];

        foreach ($parts as $part) {
            $newTokens[] = new Token([T_STRING, $part]);
            $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
        }

        array_pop($newTokens);

        $tokens->overrideRange($index, $index, $newTokens);
    }
}
