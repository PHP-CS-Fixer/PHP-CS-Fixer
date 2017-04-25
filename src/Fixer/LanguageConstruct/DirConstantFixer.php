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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFunctionReferenceFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class DirConstantFixer extends AbstractFunctionReferenceFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.',
            [new CodeSample("<?php\n\$a = dirname(__FILE__);")],
            null,
            'Risky when the function `dirname()` is overridden.'
        );
    }

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
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
            if (!$fileCandidateRight->isGivenKind([T_FILE]) || !$fileCandidateLeft->isGivenKind([T_FILE])) {
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
            if (!$tokens[$tokens->getNextNonWhitespace($closeParenthesis)]->isComment()) {
                $tokens->removeLeadingWhitespace($closeParenthesis);
            }

            $tokens[$closeParenthesis]->clear();

            // opening parenthesis removed with trailing and leading spaces
            if (!$tokens[$tokens->getNextNonWhitespace($openParenthesis)]->isComment()) {
                $tokens->removeLeadingWhitespace($openParenthesis);
            }

            $tokens->removeTrailingWhitespace($openParenthesis);
            $tokens[$openParenthesis]->clear();

            // replace constant and remove function name
            $tokens->overrideAt($fileCandidateLeftIndex, new Token([T_DIR, '__DIR__']));
            $tokens[$functionNameIndex]->clear();
        }
    }
}
