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
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Konrad Cerny <info@konradcerny.cz>
 */
class OneLineMethodArgumentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound('(');
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (
                $token->equals('(')
                && isset($tokens[$index - 3])
                && $tokens[$index - 3]->isGivenKind(T_FUNCTION)
                && $this->isNewLine($tokens[$index + 1])
            ) {
                $this->fixFunctionArguments($tokens, $index);
            }
        }
    }

    /**
     * Fix arguments for given function.
     *
     * @param Tokens $tokens             Tokens to handle
     * @param int    $startFunctionIndex Start parenthesis position
     */
    private function fixFunctionArguments(Tokens $tokens, $startFunctionIndex)
    {
        $endFunctionIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startFunctionIndex);

        for ($index = $endFunctionIndex; $index > $startFunctionIndex; --$index) {
            $token = $tokens[$index];

            if ($token->equals(')')) {
                if (!$this->isNewLine($tokens[$index - 1]) && $tokens[$index - 1]->getContent() !== '(') {
                    $this->addNewLineAfter($tokens, $index - 1, '');
                }
            }

            if ($token->equals(',')) {
                $this->fixNewLine($tokens, $index);
            }
        }
    }

    /**
     * Inserts new line after comma.
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixNewLine(Tokens $tokens, $index)
    {
        $nextTokenIndex = $index + 1;

        if ($tokens[$index + 1]->isWhitespace() && $tokens[$index + 2]->isComment()) {
            if ($this->isNewLine($tokens[$index + 3])) {
                $nextTokenIndex = $index + 3;
            }
        }

        $this->addNewLineAfter($tokens, $nextTokenIndex);
    }

    /**
     * Adds new line to the given token.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param string $indention
     */
    private function addNewLineAfter(Tokens $tokens, $index, $indention = '    ')
    {
        $newToken = new Token(array(T_WHITESPACE, "\n".$indention));

        if ($tokens[$index]->isWhitespace()) {
            $tokens->overrideAt($index, $newToken);
        } else {
            $tokens->insertAt($index + 1, $newToken);
        }
    }

    /**
     * Checks if token is new line.
     *
     * @param Token $token
     *
     * @return bool
     */
    private function isNewLine(Token $token)
    {
        $content = $token->getContent();

        return $content !== ltrim($content, "\r\n");
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Split method arguments in new lines.';
    }
}
