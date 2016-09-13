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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class FunctionTypehintDefaultFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            // check if function/method declaration
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            // find start index of argument list, but skip function imports
            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, array('(', ';', array(T_CLOSE_TAG)));
            if (!$tokens[$startParenthesisIndex]->equals('(')) {
                continue;
            }

            $this->fixFunctionDeclaration(
                $tokens,
                $startParenthesisIndex,
                $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex)
            );
        }

        return $tokens->generateCode();
    }

    /**
     * @param Tokens $tokens
     * @param int    $startParenthesisIndex
     * @param int    $endParenthesisIndex
     */
    private function fixFunctionDeclaration(Tokens $tokens, $startParenthesisIndex, $endParenthesisIndex)
    {
        for ($i = $startParenthesisIndex; $i < $endParenthesisIndex; ++$i) {
            if ($tokens[$i]->isGivenKind(CT_ARRAY_TYPEHINT)) {
                // argument already type hinted, proceed to the next variable (if there is one)
                $i = $tokens->getNextTokenOfKind($i, array(',', ')'));

                continue;
            }

            if (!$tokens[$i]->isGivenKind(T_VARIABLE)) {
                continue;
            }

            // check if the argument has a default value declared
            $nextMeaningful = $tokens->getNextMeaningfulToken($i);
            if (!$tokens[$nextMeaningful]->equals('=')) {
                continue;
            }

            // default value token index
            $nextMeaningful = $tokens->getNextMeaningfulToken($nextMeaningful);
            if (!$tokens[$nextMeaningful]->equalsAny(array(array(T_ARRAY), '['))) {
                continue;
            }

            $tokens->insertAt(
                $i,
                array(
                    new Token(array(CT_ARRAY_TYPEHINT, 'array')),
                    new Token(array(T_WHITESPACE, ' ')),
                )
            );

            $i += 2;
            $endParenthesisIndex += 2;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Function arguments with defaults should be typehinted. (Risky fixer!)';
    }
}
