<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class DoubleArrowMultilineWhitespacesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOUBLE_ARROW)) {
                continue;
            }

            $this->fixWhitespace($tokens[$index - 1]);
            $this->fixWhitespace($tokens[$index + 1]);
        }

        return $tokens->generateCode();
    }

    private function fixWhitespace(Token $token)
    {
        if (
            $token->isWhitespace()
            && !$token->isWhitespace(array('whitespaces' => " \t"))
        ) {
            $token->setContent(rtrim($token->getContent()).' ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Operator => should not be arounded by multi-line whitespaces.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the MultilineArrayTrailingComma
        return 1;
    }
}
