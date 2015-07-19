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
final class ModernizeTypesCastingFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        // contains sequence identity as key an sequence as value
        static $sequencesInvariants = array(
            'intval' => array(array(T_STRING, 'intval'), '('),
            'floatval' => array(array(T_STRING, 'floatval'), '('),
            'doubleval' => array(array(T_STRING, 'doubleval'), '('),
            'strval' => array(array(T_STRING, 'strval'), '('),
            'boolval' => array(array(T_STRING, 'boolval'), '('),
        );
        // tokens object which will be used as replacement
        static $castingTokens = array(
             'intval' => array(T_INT_CAST, '(int)'),
             'floatval' => array(T_DOUBLE_CAST, '(float)'),
             'doubleval' => array(T_DOUBLE_CAST, '(float)'),
             'strval' => array(T_STRING_CAST, '(string)'),
             'boolval' => array(T_BOOL_CAST, '(bool)'),
        );

        foreach ($sequencesInvariants as $functionIdentity => $sequenceNeeded) {
            $currIndex = 0;
            while (null !== $currIndex) {
                $matches = $tokens->findSequence($sequenceNeeded, $currIndex, $tokens->count() - 1, false);

                // stop looping if didn't find any new matches
                if (null === $matches) {
                    break;
                }

                // 0 and 1 accordingly are "intval|floatval|strval|boolval", "("
                $matches = array_keys($matches);

                // move cursor just after sequence
                $openParenthesis = $currIndex = $matches[1];
                $functionName = $matches[0];

                // skip expressions which are not function reference
                $prevTokenIndex = $tokens->getPrevMeaningfulToken($matches[0]);
                $prevToken = $tokens[$prevTokenIndex];
                if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
                    continue;
                }

                // check if something complex passed as an argument and preserve parenthesises then
                $closeParenthesis = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis);
                $countParamTokens = 0;
                for ($paramContentIndex = $openParenthesis + 1; $paramContentIndex < $closeParenthesis; ++$paramContentIndex) {
                    //not a space, means some sensible token
                    if (!$tokens[$paramContentIndex]->isGivenKind(T_WHITESPACE)) {
                        ++$countParamTokens;
                    }
                }
                $preserveParenthesises = $countParamTokens > 1;

                // special case: intval with 2 parameters shall not be processed
                if ('intval' === $functionIdentity) {
                    $tokenBeforeSecondBracket = $tokens->getPrevMeaningfulToken($closeParenthesis);
                    if (null !== $tokenBeforeSecondBracket) {
                        // we are assuming comma is at second place from the right
                        $commaCandidate = $tokens->getPrevMeaningfulToken($tokenBeforeSecondBracket);
                        if (null !== $commaCandidate && ',' === $tokens[$commaCandidate]->getContent()) {
                            continue;
                        }
                    }
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

                // perform transformation
                $replacement = array(
                    new Token($castingTokens[$functionIdentity]),
                    new Token(array(T_WHITESPACE, ' ')),
                );
                if (!$preserveParenthesises) {
                    // closing parenthesis removed with leading spaces
                    $tokens->removeLeadingWhitespace($closeParenthesis);
                    $tokens[$closeParenthesis]->clear();

                    // opening parenthesis removed with trailing spaces
                    $tokens->removeLeadingWhitespace($openParenthesis);
                    $tokens->removeTrailingWhitespace($openParenthesis);
                    $tokens[$matches[1]]->clear();
                } else {
                    // we'll need to provide a space after a casting operator
                    $tokens->removeTrailingWhitespace($functionName);
                }
                $tokens->overrideRange($functionName, $functionName, $replacement);

                // nested transformations support
                $currIndex = $functionName;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replaces intval, floatval, strval, boolval functions calls with according type casting operator.';
    }
}
