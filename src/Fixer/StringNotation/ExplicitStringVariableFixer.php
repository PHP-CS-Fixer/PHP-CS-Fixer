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
            'Convert implicit variables into explicit ones in double quoted strings.',
            [new CodeSample('<?php $a = "My name is $name!";')]
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
