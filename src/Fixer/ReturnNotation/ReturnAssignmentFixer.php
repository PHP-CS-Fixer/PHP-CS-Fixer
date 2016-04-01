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

namespace PhpCsFixer\Fixer\ReturnNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class ReturnAssignmentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound(array(T_VARIABLE, T_RETURN));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $index = $tokens->getNextTokenOfKind($index, array(';', '{'));
            if ($tokens[$index]->equals('{')) {
                $this->fixFunction($tokens, $index, $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Non global, static or reference variables should not be assigned and directly returned.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {

        // FIXME
        // should be run before white space lines, extra empty lines,
        // should be run after the EmptyStatementFixer and DuplicateSemicolonFixer (NoEmptyStatementFixer).

        return -15;
    }

    /**
     * @param Tokens $tokens
     * @param int    $start  Token index of the opening brace token of the function.
     * @param int    $end    Token index of the closing brace token of the function.
     */
    private function fixFunction(Tokens $tokens, $start, $end)
    {
        for ($index = $end; $index > $start; --$index) {
            if (!$tokens[$index]->isGivenKind(T_RETURN)) {
                continue;
            }

            // Check if returning only a variable (i.e. not the result of an expression, function call etc.)
            $returnVarIndex = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$returnVarIndex]->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $endReturnVarIndex = $tokens->getNextMeaningfulToken($returnVarIndex);
            if (!$tokens[$endReturnVarIndex]->equalsAny(array(';', array(T_CLOSE_TAG)))) {
                continue;
            }

            // Check that the variable is assigned just before it is returned
            $endAssignVarIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$endAssignVarIndex]->equals(';')) {
                continue;
            }

            $assignVarOperatorIndex = $tokens->getPrevTokenOfKind(
                $endAssignVarIndex,
                array('=', ';', '{', array(T_OPEN_TAG), array(T_OPEN_TAG_WITH_ECHO))
            );

            if (null === $assignVarOperatorIndex || !$tokens[$assignVarOperatorIndex]->equals('=')) {
                continue;
            }

            $assignVarIndex = $tokens->getPrevMeaningfulToken($assignVarOperatorIndex);
            if (!$tokens[$assignVarIndex]->equals($tokens[$returnVarIndex], false)) {
                continue;
            }

            $beforeAssignVarIndex = $tokens->getPrevMeaningfulToken($assignVarIndex);
            if (!$tokens[$beforeAssignVarIndex]->equalsAny(array(';', '{', '}'))) {
                continue;
            }

            // remove the return statement itself
            if ($tokens[$endReturnVarIndex]->equals(';')) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($endReturnVarIndex);
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($returnVarIndex);
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);

            // remove the variable and the assignment
            $tokens->clearTokenAndMergeSurroundingWhitespace($assignVarOperatorIndex);
            $tokens->clearTokenAndMergeSurroundingWhitespace($assignVarIndex);

            // insert new return statement
            $tokens->insertAt($assignVarIndex, new Token(array(T_RETURN, 'return')));
            if (!$tokens[$assignVarIndex + 1]->isWhitespace() || $tokens[$assignVarIndex + 1]->isEmpty()) {
                $tokens->insertAt($assignVarIndex + 1, new Token(array(T_WHITESPACE, ' ')));
            }
        }
    }
}
