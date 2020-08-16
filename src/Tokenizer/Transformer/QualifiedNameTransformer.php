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
 * Transform PHP 8 T_NAME_QUALIFIED and T_NAME_FULLY_QUALIFIED into PHP 7 style
 * T_STRING and T_NS_SEPARATOR tokens.
 *
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 */
final class QualifiedNameTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // must run before CurlyBraceTransformer, TypeAlternationTransformer, NamespaceOperatorTransformer
        return 1;
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
        if (!$token->equalsAny([[T_NAME_QUALIFIED], [T_NAME_FULLY_QUALIFIED]])) {
            return;
        }

        $tokens->overrideRange($index, $index, self::convertQualifiedToken($token));
    }

    /**
     * @return Token[]
     */
    private static function convertQualifiedToken(Token $token)
    {
        $names = explode('\\', $token->getContent());
        $lastNameIndex = count($names) - 1;
        $tokens = [];

        foreach ($names as $index => $name) {
            if ('' !== $name) {
                $tokens[] = new Token([T_STRING, $name]);
            }

            if ($lastNameIndex !== $index) {
                $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            }
        }

        return $tokens;
    }
}
