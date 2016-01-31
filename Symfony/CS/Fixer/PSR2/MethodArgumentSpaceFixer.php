<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\PSR2;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶4.4, ¶4.6.
 *
 * @author Kuanhung Chen <ericj.tw@gmail.com>
 */
class MethodArgumentSpaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            // looking for start of brace and skip array
            if (!$token->equals('(') || $tokens[$index - 1]->isGivenKind(T_ARRAY)) {
                continue;
            }

            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);

            // fix for method argument and method call
            for ($i = $endIndex - 1; $i > $index; --$i) {
                if (!$tokens[$i]->equals(',')) {
                    continue;
                }

                $this->fixSpace($tokens, $i);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.';
    }

    /**
     * Method to insert space after comma and remove space before comma.
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    public function fixSpace(Tokens $tokens, $index)
    {
        // remove space before comma if exist
        if ($tokens[$index - 1]->isWhitespace()) {
            $prevIndex = $tokens->getPrevNonWhitespace($index - 1);

            if (!$tokens[$prevIndex]->equalsAny(array(',', array(T_END_HEREDOC)))) {
                $tokens[$index - 1]->clear();
            }
        }

        // add space after comma if not exist
        if (!$tokens[$index + 1]->isWhitespace()) {
            $tokens->insertAt($index + 1, new Token(array(T_WHITESPACE, ' ')));
        }
    }
}
