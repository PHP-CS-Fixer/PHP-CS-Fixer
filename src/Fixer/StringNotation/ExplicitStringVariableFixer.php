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
            [new CodeSample('<?php $a = "My name is $name!";'."\n")],
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
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $prevToken = $tokens[$index - 1];
            if (
                $prevToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)
                || $prevToken->isGivenKind(T_START_HEREDOC)
                || '"' === $prevToken->getContent()
            ) {
                $tokens->overrideRange($index, $index, [
                    new Token([T_DOLLAR_OPEN_CURLY_BRACES, '${']),
                    new Token([T_STRING_VARNAME, substr($token->getContent(), 1)]),
                    new Token([CT::T_DOLLAR_CLOSE_CURLY_BRACES, '}']),
                ]);
            }
        }
    }
}
