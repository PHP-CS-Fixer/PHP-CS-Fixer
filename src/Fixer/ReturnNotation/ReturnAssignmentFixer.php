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
        // stop at 4, smallest number of tokens that could be a candidate: "<?php $a=1;return $a;"
        for ($index = count($tokens) - 1; null !== $index && $index > 4; --$index) {
            if (!$tokens[$index]->isGivenKind(T_RETURN)) {
                continue;
            }

            $index = $this->fixAssignmentBeforeReturn($tokens, $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Variables should not be assigned and directly returned.';
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
     * @param int    $index  index of T_RETURN token to fix
     *
     * @return int
     */
    private function fixAssignmentBeforeReturn(Tokens $tokens, $index)
    {
        // Check if returning only a variable (i.e. not the result of an expression, function call etc.)
        $returnVarIndex = $tokens->getNextMeaningfulToken($index);
        if (!$tokens[$returnVarIndex]->isGivenKind(T_VARIABLE)) {
            return $index;
        }

        $endReturnVarIndex = $tokens->getNextMeaningfulToken($returnVarIndex);
        if (!$tokens[$endReturnVarIndex]->equalsAny(array(';', array(T_CLOSE_TAG)))) {
            return $index;
        }

        // Check that the variable is assigned just before it is returned
        $endAssignVarIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$endAssignVarIndex]->equals(';')) {
            return $endAssignVarIndex;
        }

        $assignVarOperatorIndex = $tokens->getPrevTokenOfKind(
            $endAssignVarIndex,
            array('=', ';', array(T_OPEN_TAG), array(T_OPEN_TAG_WITH_ECHO))
        );

        if (null === $assignVarOperatorIndex || !$tokens[$assignVarOperatorIndex]->equals('=')) {
            return $assignVarOperatorIndex;
        }

        $assignVarIndex = $tokens->getPrevMeaningfulToken($assignVarOperatorIndex);
        if (!$tokens[$assignVarIndex]->equals($tokens[$returnVarIndex], false)) {
            return $assignVarIndex;
        }

        $beforeAssignVarIndex = $tokens->getPrevMeaningfulToken($assignVarIndex);
        if (!$tokens[$beforeAssignVarIndex]->equalsAny(array(';', '{', '}', array(T_OPEN_TAG), array(T_OPEN_TAG_WITH_ECHO)))) {
            return $assignVarIndex;
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

        return $assignVarIndex - 1;
    }
}
