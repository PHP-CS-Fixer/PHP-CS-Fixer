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
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
final class DirConstantFixer extends AbstractFixer
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
        $sequenceNeeded = array(array(T_STRING, 'dirname'), '(', array(T_FILE), ')');

        $currIndex = 0;
        while (null !== $currIndex) {
            $matches = $tokens->findSequence($sequenceNeeded, $currIndex, $tokens->count() - 1, false);

            // stop looping if didn't find any new matches
            if (null === $matches) {
                break;
            }

            // 0 to 3 accordingly are "dirname", "(", "__FILE__", ")"
            $matches = array_keys($matches);

            // move cursor just after sequence
            $currIndex = $matches[3];

            // skip expressions which are not function reference
            $prevTokenIndex = $tokens->getPrevMeaningfulToken($matches[0]);
            $prevToken = $tokens[$prevTokenIndex];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            // handle function reference with namespaces
            if ($prevToken->isGivenKind(T_NS_SEPARATOR)) {
                $twicePrevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                $twicePrevToken = $tokens[$twicePrevTokenIndex];
                if ($twicePrevToken->isGivenKind(array(T_NEW, T_STRING, CT_NAMESPACE_OPERATOR))) {
                    continue;
                }

                // get rid of root namespace when it used
                $tokens->removeTrailingWhitespace($prevTokenIndex);
                $tokens[$prevTokenIndex]->clear();
            }

            // closing parenthesis removed with leading spaces
            $tokens->removeLeadingWhitespace($matches[3]);
            $tokens[$matches[3]]->clear();

            // opening parenthesis removed with trailing and leading spaces
            $tokens->removeLeadingWhitespace($matches[1]);
            $tokens->removeTrailingWhitespace($matches[1]);
            $tokens[$matches[1]]->clear();

            // replace constant and remove function name
            $tokens->overrideAt($matches[2], new Token(array(T_DIR, '__DIR__')));
            $tokens[$matches[0]]->clear();
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
