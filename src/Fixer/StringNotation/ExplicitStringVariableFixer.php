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

namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class ExplicitStringVariableFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.',
            [new CodeSample(
                <<<'EOT'
<?php
$a = "My name is $name !";
$b = "I live in $state->country !";
$c = "I have $farm[0] chickens !";

EOT
            )],
            'The reasoning behind this rule is the following:'
                ."\n".'- When there are two valid ways of doing the same thing, using both is confusing, there should be a coding standard to follow'
                ."\n".'- PHP manual marks `"$var"` syntax as implicit and `"${var}"` syntax as explicit: explicit code should always be preferred'
                ."\n".'- Explicit syntax allows word concatenation inside strings, e.g. `"${var}IsAVar"`, implicit doesn\'t'
                ."\n".'- Explicit syntax is easier to detect for IDE/editors and therefore has colors/hightlight with higher contrast, which is easier to read'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $prevToken = $tokens[$index - 1];
            if (!$this->isStringPartToken($prevToken)) {
                continue;
            }

            $variableTokens = [
                $index => $token,
            ];
            $firstVariableTokenIndex = $index;
            $lastVariableTokenIndex = $index;

            $nextIndex = $index + 1;
            while (!$this->isStringPartToken($tokens[$nextIndex])) {
                $variableTokens[$nextIndex] = $tokens[$nextIndex];
                $lastVariableTokenIndex = $nextIndex;
                ++$nextIndex;
            }

            if (1 === \count($variableTokens)) {
                $tokens->overrideRange($index, $index, [
                    new Token([T_DOLLAR_OPEN_CURLY_BRACES, '${']),
                    new Token([T_STRING_VARNAME, substr($token->getContent(), 1)]),
                    new Token([CT::T_DOLLAR_CLOSE_CURLY_BRACES, '}']),
                ]);
            } else {
                foreach ($variableTokens as $variablePartIndex => $variablePartToken) {
                    if ($variablePartToken->isGivenKind(T_NUM_STRING)) {
                        $tokens[$variablePartIndex] = new Token([T_LNUMBER, $variablePartToken->getContent()]);

                        continue;
                    }

                    if ($variablePartToken->isGivenKind(T_STRING) && $tokens[$variablePartIndex + 1]->equals(']')) {
                        $tokens[$variablePartIndex] = new Token([T_CONSTANT_ENCAPSED_STRING, "'".$variablePartToken->getContent()."'"]);
                    }
                }

                $tokens->insertAt($lastVariableTokenIndex + 1, new Token([CT::T_CURLY_CLOSE, '}']));
                $tokens->insertAt($firstVariableTokenIndex, new Token([T_CURLY_OPEN, '{']));
            }
        }
    }

    /**
     * Check if token is a part of a string.
     *
     * @param Token $token The token to check
     *
     * @return bool
     */
    private function isStringPartToken(Token $token)
    {
        return $token->isGivenKind(T_ENCAPSED_AND_WHITESPACE)
            || $token->isGivenKind(T_START_HEREDOC)
            || '"' === $token->getContent()
            || 'b"' === strtolower($token->getContent())
        ;
    }
}
