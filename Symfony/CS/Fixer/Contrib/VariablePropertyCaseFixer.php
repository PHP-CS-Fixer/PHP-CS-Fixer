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
use Symfony\CS\Utils;

/**
 * @author Michal Kierat <kierate@gmail.com>
 */
class VariablePropertyCaseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $allFoundTokens = $tokens->findGivenKind(array(
            T_VARIABLE,
            T_DOLLAR_OPEN_CURLY_BRACES,
            T_STRING_VARNAME,
        ));

        foreach ($allFoundTokens as $tokenType => $foundTokens) {
            switch ($tokenType) {
                case T_VARIABLE:
                    foreach ($foundTokens as $index => $token) {
                        if ($token->getContent() === '$this') {
                            // in case of an object property check what comes
                            // after "$this->" (functions are not covered here)
                            $nextIndex = $tokens->getNextMeaningfulToken($index);
                            $nextToken = $tokens[$nextIndex];

                            if ($nextToken->isGivenKind(T_OBJECT_OPERATOR)) {
                                $furtherIndex = $tokens->getNextMeaningfulToken($nextIndex);
                                $furtherToken = $tokens[$furtherIndex];

                                $anotherIndex = $tokens->getNextMeaningfulToken($furtherIndex);
                                $anotherToken = $tokens[$anotherIndex];

                                if (!$anotherToken->equals('(')) { //not a function
                                    $this->fixUnderscoreToCamelCase($furtherToken);
                                }
                            }
                        } else {
                            // all other variables or static properties get
                            // fixed in case they contain any underscores
                            $this->fixUnderscoreToCamelCase($token);
                        }
                    }
                    break;

                case T_STRING_VARNAME:
                    foreach ($foundTokens as $index => $token) {
                        // all variables in strings get fixed
                        // in case they contain any underscores
                        $this->fixUnderscoreToCamelCase($token);
                    }
                    break;

                case T_DOLLAR_OPEN_CURLY_BRACES:
                    foreach ($foundTokens as $index => $token) {
                        // for cases where we start with "${" we want to fix
                        // what comes immediately after only if there is a "}"
                        // following that e.g. "${foo_bar}" not "${ foo_bar }"
                        $nextToken = $tokens[$index + 1];
                        $furtherToken = $tokens[$index + 2];

                        if ($furtherToken->equals('}')) {
                            $this->fixUnderscoreToCamelCase($nextToken);
                        }
                    }
                    break;

                default:
                    break;
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Convert all underscore variables and properties (some_var) '.
               'to camel case ones (someVar). Warning! Any code directly '.
               'referring to the modified properties will need adjusting.';
    }

    /**
     * Fix token casing.
     *
     * The token content is a variable. If it contains underscores then the
     * content is overriden with a camelCased equivalent.
     *
     * @param Token $token
     */
    private function fixUnderscoreToCamelCase(Token $token)
    {
        if (strstr($token->getContent(), '_') !== false) {
            $token->override(
                Utils::underscoreToCamelCase($token->getContent())
            );
        }

        return;
    }
}
