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

use Symfony\CS\AbstractFunctionReferenceFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class DirConstantFixer extends AbstractFunctionReferenceFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $currIndex = 0;
        while (null !== $currIndex) {
            $boundaries = $this->find('dirname', $tokens, $currIndex, $tokens->count() - 1);
            if (null === $boundaries) {
                return;
            }

            list($functionNameIndex, $openParenthesis, $closeParenthesis) = $boundaries;

            // analysing cursor shift, so nested expressions kept processed
            $currIndex = $openParenthesis;

            /* ensure __FILE__ is in between (...) */
            $fileCandidateRightIndex = $tokens->getPrevMeaningfulToken($closeParenthesis);
            $fileCandidateRight = $tokens[$fileCandidateRightIndex];
            $fileCandidateLeftIndex = $tokens->getNextMeaningfulToken($openParenthesis);
            $fileCandidateLeft = $tokens[$fileCandidateLeftIndex];
            if (!$fileCandidateRight->isGivenKind(array(T_FILE)) || !$fileCandidateLeft->isGivenKind(array(T_FILE))) {
                continue;
            }

            // get rid of root namespace when it used
            $namespaceCandidateIndex = $tokens->getPrevMeaningfulToken($functionNameIndex);
            $namespaceCandidate = $tokens[$namespaceCandidateIndex];
            if ($namespaceCandidate->isGivenKind(T_NS_SEPARATOR)) {
                $tokens->removeTrailingWhitespace($namespaceCandidateIndex);
                $namespaceCandidate->clear();
            }

            // closing parenthesis removed with leading spaces
            $tokens->removeLeadingWhitespace($closeParenthesis);
            $tokens[$closeParenthesis]->clear();

            // opening parenthesis removed with trailing and leading spaces
            $tokens->removeLeadingWhitespace($openParenthesis);
            $tokens->removeTrailingWhitespace($openParenthesis);
            $tokens[$openParenthesis]->clear();

            // replace constant and remove function name
            $tokens->overrideAt($fileCandidateLeftIndex, new Token(array(T_DIR, '__DIR__')));
            $tokens[$functionNameIndex]->clear();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replaces dirname(__FILE__) expression with equivalent __DIR__ constant.';
    }
}
