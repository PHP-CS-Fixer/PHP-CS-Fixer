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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Bram Gotink <bram@gotink.me>
 */
class NamespaceNoLeadingWhitespaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $beforeNamespace = $tokens[$index - 1];

            if (!$beforeNamespace->isWhitespace()) {
                if (!self::endsWithWhitespace($beforeNamespace->getContent())) {
                    $tokens->insertAt($index, new Token(array(T_WHITESPACE, "\n")));
                }

                continue;
            }

            $lastNewline = strrpos($beforeNamespace->getContent(), "\n");

            if (false === $lastNewline) {
                $beforeBeforeNamespace = $tokens[$index - 2];

                if (self::endsWithWhitespace($beforeBeforeNamespace->getContent())) {
                    $beforeNamespace->clear();
                } else {
                    $beforeNamespace->setContent(' ');
                }
            } else {
                $beforeNamespace->setContent(substr($beforeNamespace->getContent(), 0, $lastNewline + 1));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The namespace declaration line shouldn\'t contain leading whitespace.';
    }

    private static function endsWithWhitespace($str)
    {
        if ('' === $str) {
            return false;
        }

        return '' === trim(substr($str, -1));
    }
}
