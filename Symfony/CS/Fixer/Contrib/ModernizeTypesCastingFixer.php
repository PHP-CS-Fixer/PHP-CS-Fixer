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
use Symfony\CS\Functions\FunctionArgumentsUtil;
use Symfony\CS\Functions\FunctionDefinitionUtil;
use Symfony\CS\Functions\FunctionReference\Finder;
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
        // replacement patterns
        static $replacement = array(
             'intval' => array(T_INT_CAST, '(int)'),
             'floatval' => array(T_DOUBLE_CAST, '(float)'),
             'doubleval' => array(T_DOUBLE_CAST, '(float)'),
             'strval' => array(T_STRING_CAST, '(string)'),
             'boolval' => array(T_BOOL_CAST, '(bool)'),
        );

        foreach ($replacement as $functionIdentity => $newToken) {
            $isFunctionDefinedInScope = FunctionDefinitionUtil::isDefinedInScope($functionIdentity, $tokens);

            $currIndex = 0;
            while (null !== $currIndex) {
                // try getting function reference and translate boundaries for humans
                $boundaries = Finder::find($functionIdentity, $tokens, $currIndex, $tokens->count() - 1);
                if (null === $boundaries) {
                    // next function search, as current one not found
                    continue 2;
                }
                list($functionName, $openParenthesis, $closeParenthesis) = $boundaries;

                // analysing cursor shift
                $currIndex = $openParenthesis;

                // special case: intval with 2 parameters shall not be processed (base conversion)
                if ('intval' === $functionIdentity) {
                    $parametersCount = FunctionArgumentsUtil::countArguments($openParenthesis, $closeParenthesis, $tokens);
                    if ($parametersCount > 1) {
                        continue;
                    }
                }

                // check if something complex passed as an argument and preserve parenthesises then
                $countParamTokens = 0;
                for ($paramContentIndex = $openParenthesis + 1; $paramContentIndex < $closeParenthesis; ++$paramContentIndex) {
                    //not a space, means some sensible token
                    if (!$tokens[$paramContentIndex]->isGivenKind(T_WHITESPACE)) {
                        ++$countParamTokens;
                    }
                }
                $preserveParenthesises = $countParamTokens > 1;

                // analyse namespace specification (root one or none) and decide what to do
                $prevTokenIndex = $tokens->getPrevMeaningfulToken($functionName);
                if ($tokens[$prevTokenIndex]->isGivenKind(T_NS_SEPARATOR)) {
                    // get rid of root namespace when it used
                    $tokens->removeTrailingWhitespace($prevTokenIndex);
                    $tokens[$prevTokenIndex]->clear();
                } elseif ($isFunctionDefinedInScope) {
                    // skip analysis if function is defined in the scope, so this is a referenced call
                    continue;
                }

                // perform transformation
                $replacementSequence = array(
                    new Token($newToken),
                    new Token(array(T_WHITESPACE, ' ')),
                );
                if (!$preserveParenthesises) {
                    // closing parenthesis removed with leading spaces
                    $tokens->removeLeadingWhitespace($closeParenthesis);
                    $tokens[$closeParenthesis]->clear();

                    // opening parenthesis removed with trailing spaces
                    $tokens->removeLeadingWhitespace($openParenthesis);
                    $tokens->removeTrailingWhitespace($openParenthesis);
                    $tokens[$openParenthesis]->clear();
                } else {
                    // we'll need to provide a space after a casting operator
                    $tokens->removeTrailingWhitespace($functionName);
                }
                $tokens->overrideRange($functionName, $functionName, $replacementSequence);

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
